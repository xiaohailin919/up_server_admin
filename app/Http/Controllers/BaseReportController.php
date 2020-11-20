<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\Geo;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Unit;
use App\Utils\ChannelAdapter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaseReportController extends BaseController
{
    protected $perPage = 25;
    protected $pageName = 'page';
    protected $timezoneList = [
        '-12' => 'UTC-12',
        '-11' => 'UTC-11',
        '-10' => 'UTC-10',
        '-9'  => 'UTC-9',
        '-8'  => 'UTC-8',
        '-7'  => 'UTC-7',
        '-6'  => 'UTC-6',
        '-5'  => 'UTC-5',
        '-4'  => 'UTC-4',
        '-3'  => 'UTC-3',
        '-2'  => 'UTC-2',
        '-1'  => 'UTC-1',
        '0'   => 'UTC+0',
        '1'   => 'UTC+1',
        '2'   => 'UTC+2',
        '3'   => 'UTC+3',
        '4'   => 'UTC+4',
        '5'   => 'UTC+5',
        '6'   => 'UTC+6',
        '7'   => 'UTC+7',
        '8'   => 'UTC+8',
        '9'   => 'UTC+9',
        '10'  => 'UTC+10',
        '11'  => 'UTC+11',
        '12'  => 'UTC+12',
    ];

    protected static function getScTypeMap()
    {
        return [
            '2' => 'Pic',
            '1' => 'Video',
            '0' => 'Unknown',
        ];
    }

    protected static function getFormatMap()
    {
        return (new Placement())->getFormatMap();
    }

    protected static function getNwFirmMap()
    {
        return (new NetworkFirm())->newQuery()->select('id', 'name')->get();
    }

    protected static function getPlatformMap()
    {
        return (new App())->getPlatformMap();
    }

    protected static function getSystemMap()
    {
        return (new App())->getSystemMap();
    }

    protected static function getGeoMap()
    {
        return (new Geo())->getGeoMap();
    }

    protected function getChannelMap()
    {
        return (new Publisher())->getChannelMap();
    }

    protected static function getIndexDateRange(Request $request, $defaultDay = 6)
    {
        $reqDateRange = $request->input('daterange', '');
        if ($reqDateRange == '') {
            $startTime = time() - 86400 * $defaultDay;
            $endTime = time();

        } else {
            list($s, $e) = explode('-', $reqDateRange);
            $startTime = strtotime(trim($s));
            $endTime = strtotime(trim($e));
        }

        return [
            'time' => ['start' => $startTime, 'end' => $endTime],
            'string' => ['start' => date('m/d/Y', $startTime), 'end' => date('m/d/Y', $endTime)],
        ];
    }

    /**
     * 不同渠道登录后，根据model，对queryBuilder添加额外的where查询条件
     * @param $model
     * @param $builder
     * @param string $channel
     */
    protected function adaptModelQueryBuilder($model, &$builder, $channel="all")
    {
        if(($model instanceof App || $model instanceof Placement || $model instanceof Unit)){
            if(ChannelAdapter::isInChannelList(Auth::id())){//渠道管理员
                ChannelAdapter::adaptReportQueryBuilder($builder, Auth::id());
            } else if($channel !== "all"){ // TopOn管理员选择了某个渠道
                switch($channel){
                    case 0://TopOn
                        $builder->whereNotBetween("publisher_id", [1000000, 2000000]);
                        break;
                    case 10000:// 233
                        $builder->whereBetween("publisher_id", [1000000, 2000000]);
                        break;
                }
            }
        }

        if($model instanceof Publisher) {
            if(ChannelAdapter::isInChannelList(Auth::id())){
                Log::info("publisherModel");
                ChannelAdapter::adaptPublisherQueryBuilder($builder, Auth::id());
            } else if($channel !== "all"){
                switch($channel){
                    case 0://TopOn
                        $builder->whereNotBetween("id", [1000000, 2000000]);
                        break;
                    case 10000:// 233
                        $builder->whereBetween("id", [1000000, 2000000]);
                        break;
                }
            }
        }
    }

    protected function getIdListByName($model, $idField, $nameField, $searchName, $channel="all")
    {
        $builder = $model->queryBuilder()
            ->select([$idField]);
        $this->adaptModelQueryBuilder($model, $builder, $channel);
        return array_column($builder->where($nameField, 'like', "%{$searchName}%")
            ->get()->toArray(), $idField);
    }

    protected function getIdListByField($model, $idField, $searchField, $searchFieldVal)
    {
        $builder = $model->queryBuilder()
            ->select([$idField])->where($searchField, $searchFieldVal);
        $this->adaptModelQueryBuilder($model, $builder);
        return array_column($builder->get()->toArray(), $idField);
    }

    protected function getIdByField($model, $idField, $searchField, $searchFieldVal)
    {
        $builder = $model->queryBuilder()
            ->select([$idField])->where($searchField, $searchFieldVal);
        $this->adaptModelQueryBuilder($model, $builder);
        $row = $builder->first();
        if (empty($row)) {
            return 0;
        }
        return $row[$idField];
    }

    protected function getIdInListByField($model, $idField, $searchField, $searchFieldValList)
    {
        $builder = $model::query()
            ->select([$idField])->whereIn($searchField, $searchFieldValList);
        $this->adaptModelQueryBuilder($model, $builder);
        return array_column($builder->get()->toArray(), $idField);
    }

    /**
     * 检查是否使用新BI接口（采用GreenPlum）
     * @param Request $request
     * @return bool
     */
    protected function checkRportApiV2(Request $request){
        $uri = $request->route()->uri;
        $uriArray = [
            'full-report-v2',
            'chart-report-v2',
        ];
        if(in_array($uri, $uriArray)){
            return true;
        }
        return false;
    }

    /**
     * 获取BI接口（V1采用MySQL，V2采用GreenPlum）
     * @param Request $request
     * @param $url
     * @return string
     */
    protected function getReportApi(Request $request, $url){
        if($this->checkRportApiV2($request)){
            return str_replace('/api/v1/', '/api/v2/', $url);
        }
        return $url;
    }
}
