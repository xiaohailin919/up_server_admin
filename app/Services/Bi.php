<?php
/**
 * Created by PhpStorm.
 * User: Liu
 * Date: 2020/1/7
 * Time: 11:44 AM
 */

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException as HttpRequestException;
use GuzzleHttp\RequestOptions as HttpRequestOptions;

use App\Facades\AuthUser;

use App\Models\MySql\Unit;
use App\Models\MySql\Geo;
use App\Models\MySql\Publisher;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Scenario;
use App\Models\MySql\Segment;

use App\Helpers\Encryption;
use App\Helpers\Translator;


class Bi
{
    public $groupByNwFirm  = false;
    public $groupByNetwork = false;

    public $fieldNotCompare = [
        'date_time',
        'publisher_id',
        'publisher_type',
        'publisher_name',
        'app_id',
        'app_uuid',
        'app_name',
        'placement_id',
        'placement_uuid',
        'placement_name',
        'scenario_uuid',
        'scenario_name',
        'format',
        'format_name',
        'segment_id',
        'segment_name',
        'geo_short',
        'geo_name',
//        'network_id',
        'nw_firm_id',
        'nw_firm_name',
        'unit_id',
        'unit_name',
        'sdk_version',
        'app_version'
    ];

    /**
     * 通过Http获取数据
     * @param  string $url
     * @param  array  $param
     * @return array
     */
    public function getHttpData($url, $param){
        Log::info("getHttpData URL: " . $url);
        Log::info("getHttpData URL Param: " . json_encode($param));

        $httpClient = new HttpClient();
        $httpOption = [
            HttpRequestOptions::JSON    => $param,
            HttpRequestOptions::TIMEOUT => 60
        ];

        try {
            // 调用BI接口获取数据
            $result = $httpClient->post($url, $httpOption);

            if($result->getStatusCode() != 200){
                return [];
            }

            return json_decode($result->getBody(), true);
        } catch (HttpRequestException $e) {
            // 异常处理
        }
    }

    /**
     * 获取综合报表数据
     * @param  array $param
     * @return array
     */
    public function getFullReport($param){

    }

    /**
     * 填充Filter扩展数据
     * @param  array $data
     * @param  array $groupBy
     * @return mixed
     */
    public function fillFilterInfo($data, $groupBy){
        if(empty($data) || empty($groupBy)){
            return $data;
        }
        if(count($groupBy) == 1 && $groupBy[0] == 'date_time'){
            return $data;
        }
        $filter = $info = [
            'publisher_id' => [],
            'app_id'       => [],
            'placement_id' => [],
            'scenario'     => [],
            'unit_id'      => [],
            'geo_short'    => [],
            'nw_firm_id'   => [],
            'group_id'     => [],
            'format'       => [],
        ];
        foreach($data as $key => $val){
            // 提取group by对应的各个纬度ID
            foreach($filter as $filterKey => $filterVal){
                if(in_array($filterKey, $groupBy)){
                    $filter[$filterKey][] = $val[$filterKey];
                }
            }
        }

        // Publisher
        if(!empty($filter['publisher_id'])){
            $publisher = Publisher::select(
                'id   as publisher_id',
                'name as publisher_name',
                'mode as publisher_mode'
            )
                ->whereIn('id', $filter['publisher_id'])
                ->get()
                ->toArray();
            foreach($publisher as &$val){
                $val['publisher_type'] = 'White';
                if($val['publisher_mode'] == 2){
                    $val['publisher_type'] = 'Black';
                }
            }
            $info['publisher_id'] = array_column($publisher, null, 'publisher_id');
        }
        // App
        if(!empty($filter['app_id'])){
            $app = App::select(
                'id   as app_id',
                'uuid as app_uuid',
                'name as app_name',
                'platform',
                'platform_app_id',
                'bundle_id'
            )
                ->whereIn('id', $filter['app_id'])
                ->get()
                ->toArray();
            foreach($app as &$val){
                $val['platform_name'] = 'iOS';
                if($val['platform'] == 1){
                    $val['platform_name'] = 'Android';
                }
            }
            $info['app_id'] = array_column($app, null, 'app_id');
        }
        // Placement
        if(!empty($filter['placement_id'])){
            $placement = Placement::select(
                'id   as placement_id',
                'uuid as placement_uuid',
                'name as placement_name'
            )
                ->whereIn('id', $filter['placement_id'])
                ->get()
                ->toArray();
            $info['placement_id'] = array_column($placement, null, 'placement_id');
        }
        // Scenario
        if(!empty($filter['scenario'])){
            $placement = Scenario::select(
                    'uuid as scenario_uuid',
                    'name as scenario_name'
                )
                ->whereIn('uuid', $filter['scenario'])
                ->get()
                ->toArray();
            $info['scenario'][] = [
                'scenario_uuid' => '1',
                'scenario_name' => 'Default',
            ];
            $info['scenario'] = array_column($placement, null, 'scenario_uuid');
        }
        // Unit
        if(!empty($filter['unit_id'])){
            $unit = Unit::select(
                'id   as unit_id',
                'name as unit_name'
            )
                ->whereIn('id', $filter['unit_id'])
                ->get()
                ->toArray();
            $info['unit_id'] = array_column($unit, null, 'unit_id');
        }
        // Network Firm
        if(!empty($filter['nw_firm_id'])){
            $firms = NetworkFirm::query()
                ->leftJoin('publisher as p', 'p.id', '=', 'network_firm.publisher_id')
                ->select(['network_firm.id as nw_firm_id', 'network_firm.name as nw_firm_name','p.id as nw_firm_publisher_id'])
                ->selectRaw('IFNULL(p.name, "-") as nw_firm_publisher_name')
                ->whereIn('network_firm.id', $filter['nw_firm_id'])
                ->get()
                ->toArray();
            foreach ($firms as $firm) {
                if ($firm['nw_firm_id'] > NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY) {
                    $firm['nw_firm_name'] = $firm['nw_firm_publisher_name'] . '(' . $firm['nw_firm_publisher_id'] . ') | ' . $firm['nw_firm_name'];
                }
            }
            $info['nw_firm_id'] = array_column($firms, null, 'nw_firm_id');
        }
        // Segment
        if(!empty($filter['group_id'])){
            $segment = Segment::select(
                    'uuid as segment_uuid',
                    'id   as segment_id',
                    'name as segment_name'
                )
                ->whereIn('id', $filter['group_id'])
                ->get()
                ->toArray();
            $segment[] = [
                'segment_id'   => 0,
                'segment_name' => 'Default',
            ];
            $info['group_id'] = array_column($segment, null, 'segment_id');
        }
        // Geo
        if(!empty($filter['geo_short'])){
            $geo = Geo::select(
                'short as geo_short',
                'name  as geo_name'
            )
                ->whereIn('short', $filter['geo_short'])
                ->get()
                ->toArray();
            $info['geo_short'] = array_column($geo, null, 'geo_short');
        }
        // Format
        if(!empty($filter['format'])){
            $formatMap = (new Placement())->getFormatMap();
            foreach($formatMap as $key => $val){
                $info['format'][$key] = [
                    'format'      => $key,
                    'format_name' => $val
                ];
            }
        }
//        print_r($info);
//        exit;

        // 合并数据
        foreach($data as $key => &$val){
            // 提取group by对应的各个纬度ID
            foreach($info as $infoKey => $infoVal){
                if(in_array($infoKey, $groupBy) && !empty($filter[$infoKey])){
                    if(!isset($infoVal[$val[$infoKey]])){
//                        unset($data[$key]);
                        continue;
                    }
                    $val = array_merge($val, $infoVal[$val[$infoKey]]);
                }
            }
        }

        return array_values($data);
    }

    /**
     * 填充对比数据并返回
     * @param  array  $data
     * @param  array  $dataCompare
     * @param  string $fieldKey
     * @param  array  $extra
     * @return array
     */
    public function fillFullReportCompare($data, $dataCompare, $fieldKey, $extra = []){
        $field = explode('.', $fieldKey);
        if(count($field) < 1 || count($field) > 2){
            return $data;
        }

        $data['list_compare']        = [];
        $data['list_compare_result'] = [];

        foreach($data['list'] as $key => $val){
            // 从对比数据中找到对应数据
            foreach($dataCompare['list'] as $v){
                if($fieldKey === 'date_time'){
                    if(isset($extra[$val['date_time']]) && (string)$v['date_time'] === (string)$extra[$val['date_time']]){
                        $data['list_compare'][$key] = $v;
                        break;
                    }
                }else if(count($field) > 1){
                    if($val[$field[0]][$field[1]] == $v[$field[0]][$field[1]]){
                        $data['list_compare'][$key] = $v;
                        break;
                    }
                }else{
                    if($val[$field[0]] == $v[$field[0]]){
                        $data['list_compare'][$key] = $v;
                        break;
                    }
                }

                continue;
            }
            if(empty($data['list_compare'][$key])){
                // 初始化空数据
                $data['list_compare'][$key] = [];
                if ($fieldKey === 'date_time') {
                    $data['list_compare'][$key]['date_time'] = data_get($extra, $val['date_time'], '-');
                }
            }
            // 对比
            foreach($val as $subKey => $subVal){
                if(in_array($subKey, $this->fieldNotCompare)){
                    continue;
                }
                if($subVal <= 0 && (!isset($data['list_compare'][$key][$subKey]) || $data['list_compare'][$key][$subKey] <= 0)){
                    // 分子 分母 都为零
                    $data['list_compare_result'][$key][$subKey] = '';
                }else if(!isset($data['list_compare'][$key][$subKey]) || $data['list_compare'][$key][$subKey] <= 0){
                    // 分母为零
                    $data['list_compare_result'][$key][$subKey] = 5;
                }else{
                    $data['list_compare_result'][$key][$subKey] =
                        ((float)$subVal - (float)$data['list_compare'][$key][$subKey]) / (float)$data['list_compare'][$key][$subKey];
                    $data['list_compare_result'][$key][$subKey] = round($data['list_compare_result'][$key][$subKey], 4);
                }
                // 格式化
                $data['list_compare_result'][$key][$subKey] = round($data['list_compare_result'][$key][$subKey], 4) * 100 . '%';
            }
        }

        return $data;
    }

    /**
     * 填充综合报表单行数据（导出数据）
     * @param  array  $row
     * @param  bool   $total
     * @param  string $dateRange
     * @return array
     */
    public function fillFullReportRow($row, $total = false, $dateRange = ''){
        if($total){
            $row['date_time'] = $dateRange;
        }else if(!in_array('date_time', array_keys($row))){
            $row['date_time'] = $dateRange;
        }
        foreach ($row as $kk => $vv) {
            if ($kk == "app_name") {
                $row[$kk] = '-';
                $row["app_platform"] = '-';
            } else if ($kk == "adhelper") {
                $row['unit_name'] = '-';
            } else if (in_array($kk, $this->fieldNotCompare) && $kk != 'date_time') {
                $row[$kk] = '-';
            }

            if (empty($vv)) {
                $row[$kk] = "-";
            }
        }

        return $row;
    }
}