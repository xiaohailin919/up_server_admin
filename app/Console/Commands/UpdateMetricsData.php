<?php

namespace App\Console\Commands;

use App\Models\MySql\DataSortMetrics;
use App\Services\Bi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateMetricsData extends Command
{
    const DIMENSION_DAY   = 'day';
    const DIMENSION_WEEK  = 'week';
    const DIMENSION_MONTH = 'month';

    const TYPE_APP = 'app';
    const TYPE_PLACEMENT = 'placement';

    protected $signature = 'data:metrics {--dimension=day} {--type=app}';

    protected $description = '更新 MySql 中临时数据表的数据';

    private $param;
    private $biService;
    private $idList;

    public function __construct() {
        parent::__construct();
        $this->biService = new Bi();
    }

    public function handle()
    {
        $this->prepareParam($this->option('dimension'), $this->option('type'));

        $biParam = [
            'start_time' => $this->param['beg_date'],
            'end_time'   => $this->param['end_date'],
            'timezone'   => 108,
            'format'     => -1,
            'is_cn_sdk'  => -1,
            'offset'     => 0,
            'limit'      => 2000,
            'group_by'   => $this->param['group_by'],
        ];
        Log::info('Update Metrics Data getting BI data: ' . ' API: ' . $this->param['url'] . '; param: ', $biParam);

        /* 先拉一次，拉回来分析总数，不够就循环拉 */
        $response = $this->biService->getHttpData($this->param['url'], $biParam);
        $total = $response['data']['count'] ?? 0;
        $data = $response['data']['list'] ?? [];
        $sql = $this->generateSql($data);

        if ($total == 0 || count($data) == 0) {
            Log::info('Update Metrics Data: BI return 0 data.');
            echo "BI return 0 data";
            return;
        }

        $extraIdList = [];
        try {
            DB::beginTransaction();
            if (DataSortMetrics::query()->where('type', $this->param['type'])->where('period_type', $this->param['day1'])->exists()) {
                /* 将大前天的数据全部删除 */
                DataSortMetrics::query()->where('type', $this->param['type'])->where('period_type', $this->param['day3'])->delete();
                /* 将前天该维度的更新为大前天的 */
                DataSortMetrics::query()->where('type', $this->param['type'])->where('period_type', $this->param['day2'])->update(['period_type' => $this->param['day3']]);
                /* 将昨天该维度的更新为前天的 */
                DataSortMetrics::query()->where('type', $this->param['type'])->where('period_type', $this->param['day1'])->update(['period_type' => $this->param['day2']]);
                /* 将昨天该维度的数据全部删除 */
                DataSortMetrics::query()->where('type', $this->param['type'])->where('period_type', $this->param['day1'])->delete();
            }
            /* 先写入第一次拿的数据 */
            DB::insert($sql);

            /* 大于 5000 则继续插入 */
            if ($total > $biParam['limit']) {
                while (($biParam['offset'] += $biParam['limit']) < $total) {
                    echo "Data insert: " . number_format($biParam['offset'] / $total, 2) * 100 . '%' . PHP_EOL;
                    $response = $this->biService->getHttpData($this->param['url'], $biParam);
                    $data = $response['data']['list'] ?? [];
                    $sql = $this->generateSql($data);
                    if ($sql != '') {
                        DB::insert($sql);
                    }
                    $biParam['offset'] += $biParam['limit'];
                }
            }

            /* 将 BI 中没有数据补入，让该中间表拥有所有 app、Placement 记录，可以在作为主表来关联 app 表 */
            if ($this->param['type'] == DataSortMetrics::TYPE_APP) {
                $extraIdList = \App\Models\MySql\App::query()->whereNotIn('id', $this->idList)->get(['id'])->getQueueableIds();
            } else {
                $extraIdList = \App\Models\MySql\Placement::query()->whereNotIn('id', $this->idList)->get(['id'])->getQueueableIds();
            }

            $sql = $this->generateDefaultSql($extraIdList);
            if ($sql != '') {
                DB::insert($sql);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Metrics Data got BI data: Insert fail：' . $e->getMessage());
            $this->info("Exception: " . $e->getMessage());
        }

        echo "Total insert: " . ($total + count($extraIdList));
    }

    /**
     * 根据输入，配置好参数
     *
     * @param $dimension
     * @param $type
     */
    private function prepareParam($dimension, $type) {
        $this->param = [];

        /* BI 接口 */
        $this->param['url'] = env('BI_SERVICE_ADMIN_REPORT_V2');

        switch ($dimension) {
            case self::DIMENSION_WEEK:
                $startDate = (int)date('Ymd', time() - 60 * 60 * 24 * 8);
                break;
            case self::DIMENSION_MONTH:
                $year  = date('Y');
                $month = str_pad((int)date('m') - 1, 2, '0', STR_PAD_LEFT);
                $day   = str_pad((int)date('d') - 1, 2, '0', STR_PAD_LEFT);
                $startDate = (int)($year . $month . $day);
                break;
            default:
                $startDate = (int)date('Ymd', time() - 60 * 60 * 24);
        }

        $this->param['beg_date'] = $startDate;
        $this->param['end_date'] = (int)date('Ymd', time() - 60 * 60 * 24);
        $this->param['group_by'] = $type == self::TYPE_PLACEMENT ? ['placement_id'] : ['app_id'];

        /* 准备好数据库需要的参数 */
        if ($dimension == self::DIMENSION_MONTH) {
            $periodTypeOneDaysAgo   = DataSortMetrics::PERIOD_TYPE_MONTH_ONE_DAY_AGO;
            $periodTypeTwoDaysAgo   = DataSortMetrics::PERIOD_TYPE_MONTH_TWO_DAY_AGO;
            $periodTypeThreeDaysAgo = DataSortMetrics::PERIOD_TYPE_MONTH_THREE_DAY_AGO;
        } else if ($dimension == self::DIMENSION_WEEK) {
            $periodTypeOneDaysAgo   = DataSortMetrics::PERIOD_TYPE_WEEK_ONE_DAY_AGO;
            $periodTypeTwoDaysAgo   = DataSortMetrics::PERIOD_TYPE_WEEK_TWO_DAY_AGO;
            $periodTypeThreeDaysAgo = DataSortMetrics::PERIOD_TYPE_WEEK_THREE_DAY_AGO;
        } else {
            $periodTypeOneDaysAgo   = DataSortMetrics::PERIOD_TYPE_DAY_ONE_DAY_AGO;
            $periodTypeTwoDaysAgo   = DataSortMetrics::PERIOD_TYPE_DAY_TWO_DAY_AGO;
            $periodTypeThreeDaysAgo = DataSortMetrics::PERIOD_TYPE_DAY_THREE_DAY_AGO;
        }

        $this->param['day1'] = $periodTypeOneDaysAgo;
        $this->param['day2'] = $periodTypeTwoDaysAgo;
        $this->param['day3'] = $periodTypeThreeDaysAgo;
        $this->param['type'] = $type == self::TYPE_PLACEMENT ? DataSortMetrics::TYPE_PLACEMENT : DataSortMetrics::TYPE_APP;
    }

    /**
     * 生成批量插入的 sql
     *
     * @param $data
     * @return string
     */
    private function generateSql($data): string
    {
        if (count($data) == 0) {
            return "";
        }
        $sql = "insert into data_sort_metrics (`type`, `app_id`, `placement_id`, `period_type`, `revenue`, `load`, `request`, `impression`, `dau`, `create_time`) values ";
        foreach ($data as $datum) {

            $this->idList[] = $this->param['type'] == DataSortMetrics::TYPE_APP ? $datum['app_id'] : $datum['placement_id'];

            $sql .= "("
                . $this->param['type']  . "," . $datum['app_id']         . "," . $datum['placement_id'] . ","
                . $this->param['day1']  . "," . $datum['revenue']        . "," . $datum['sdk_loads']    . ","
                . $datum['sdk_request'] . "," . $datum['sdk_impression'] . "," . $datum['dau']          . ",'"
                . date('Y-m-d H:i:s') . "'),";
        }
        $sql = substr($sql, 0, -1);
        return $sql . ";";
    }

    private function generateDefaultSql($idList): string
    {
        if (count($idList) == 0) {
            return "";
        }
        $sql = "insert into data_sort_metrics (`type`, `app_id`, `placement_id`, `period_type`, `revenue`, `load`, `request`, `impression`, `dau`, `create_time`) values ";
        if ($this->param['type'] == DataSortMetrics::TYPE_APP) {
            foreach ($idList as $id) {
                $sql .= "(" . $this->param['type'] . "," . $id . ",0," . $this->param['day1'] . ",0,0,0,0,0,'" . date('Y-m-d H:i:s') . "'),";
            }
        } else {
            foreach ($idList as $id) {
                $sql .= "(" . $this->param['type'] . ",0," . $id . "," . $this->param['day1'] . ",0,0,0,0,0,'" . date('Y-m-d H:i:s') . "'),";
            }
        }
        $sql = substr($sql, 0, -1);
        return $sql . ";";
    }
}
