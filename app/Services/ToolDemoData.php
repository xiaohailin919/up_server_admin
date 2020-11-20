<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/12/14
 * Time: 18:00
 */

namespace App\Services;

use App\Models\MySql\ReportTk as ReportTkModel;
use App\Models\MySql\ReportUnit as ReportUnitModel;
use App\Models\MySql\ReportEstimate as ReportEstimateModel;
use App\Models\MySql\ReportDauApp as ReportDauAppModel;

use App\Models\MySql\ReportUnitDemo as ReportUnitDemoModel;
use App\Models\MySql\ReportTkDemo as ReportTkDemoModel;
use App\Models\MySql\ReportEstimateDemo as ReportEstimateDemoModel;
use App\Models\MySql\ReportDauAppDemo as ReportDauAppDemoModel;

use Illuminate\Support\Facades\Log;

class ToolDemoData
{
    public static function toDealDemoReport($day)
    {
        $publisherId = 35;
        $appId1 = 78;
        $appId2 = 77;
        if ($day == 20181208) {
            $yesterday = $day;
        } else {
            $yesterday = date('Ymd', strtotime($day) - 24 * 3600);
        }

        $where['publisher_id'] = $publisherId;
        $where['date_time'] = $yesterday;

        $reportTkModel = new ReportTkModel();
        $reportTkDemoModel = new ReportTkDemoModel();
        $reportUnitModel = new ReportUnitModel();
        $reportUnitDemoModel = new ReportUnitDemoModel();
        $reportEstimateModel = new ReportEstimateModel();
        $reportEstimateDemoModel = new ReportEstimateDemoModel();
        $reportDauAppModel = new ReportDauAppModel();
        $reportDauAppDemoModel = new ReportDauAppDemoModel();

        $type = "+";
        $typeArr = ["+", "-"];
        if ($day == '20181208' || $day == '20181209') {
            $count = $reportTkModel->getCount($where);
        } else {
            $count = $reportTkDemoModel->getCount($where);
            $type = $typeArr[array_rand($typeArr, 1)];
        }
        $start = 0;
        $length = 100;

        //清理
        $queryReportTkDemoModel = $reportTkDemoModel->queryBuilder();
        $queryReportTkDemoModel->where('date_time', $day)->delete();
        $queryReportUnitDemoModel = $reportUnitDemoModel->queryBuilder();
        $queryReportUnitDemoModel->where('date_time', $day)->delete();
        $queryReportEstimateDemoModel = $reportEstimateDemoModel->queryBuilder();
        $queryReportEstimateDemoModel->where('date_time', $day)->delete();
        $queryReportDauAppDemoModel = $reportDauAppDemoModel->queryBuilder();
        $queryReportDauAppDemoModel->where('date_time', $day)->delete();

        while ($start < $count) {
            if ($day == '20181208' || $day == '20181209') {
                $list = $reportTkModel->getBatch([], $where, $start, $length);
            } else {
                $list = $reportTkDemoModel->getBatch([], $where, $start, $length);
            }

            foreach ($list as $k => $v) {
                $tkData = $v;
                unset($tkData["date_time"]);
                unset($tkData["id"]);
                $tkData['date_time'] = $day;
                if ($day == '20181208') {
                    $tkData['loads'] = $tkData['loads'];
                    $tkData['filled_loads'] = $tkData['filled_loads'];
                    $tkData['request'] = $tkData['request'];
                    $tkData['filled_request'] = $tkData['filled_request'];
                    $tkData['impression'] = $tkData['impression'];
                    $tkData['click'] = $tkData['click'];
                } else if ($day == '20181209') {
                    $tkData['loads'] = $tkData['loads'] * 10;
                    $tkData['filled_loads'] = $tkData['filled_loads'] * 10;
                    $tkData['request'] = $tkData['request'] * 10;
                    $tkData['filled_request'] = $tkData['filled_request'] * 10;
                    $tkData['impression'] = $tkData['impression'] * 10;
                    $tkData['click'] = $tkData['click'];
                } else {
                    if ($type == "-") {
                        $tkData['loads'] = $tkData['loads'] - floor(rand(0, 10) * $tkData['loads'] / 100);
                        $tkData['filled_loads'] = $tkData['filled_loads'] - floor(rand(0, 10) * $tkData['filled_loads'] / 100);
                        $tkData['request'] = $tkData['request'] - floor(rand(0, 10) * $tkData['request'] / 100);
                        $tkData['filled_request'] = $tkData['filled_request'] - floor(rand(0, 10) * $tkData['filled_request'] / 100);
                        $tkData['impression'] = $tkData['impression'] - floor(rand(0, 10) * $tkData['impression'] / 100);
                        $tkData['click'] = $tkData['click'] - floor(rand(0, 10) * $tkData['click'] / 100);
                    } else {
                        $tkData['loads'] = $tkData['loads'] + floor(rand(0, 10) * $tkData['loads'] / 100);
                        $tkData['filled_loads'] = $tkData['filled_loads'] + floor(rand(0, 10) * $tkData['filled_loads'] / 100);
                        $tkData['request'] = $tkData['request'] + floor(rand(0, 10) * $tkData['request'] / 100);
                        $tkData['filled_request'] = $tkData['filled_request'] + floor(rand(0, 10) * $tkData['filled_request'] / 100);
                        $tkData['impression'] = $tkData['impression'] + floor(rand(0, 10) * $tkData['impression'] / 100);
                        $tkData['click'] = $tkData['click'] + floor(rand(0, 10) * $tkData['click'] / 100);
                    }

                }

                $post = $queryReportTkDemoModel->insert($tkData);
                if ($post) {
                    Log::info('insert ReportTkDemoModel request data succ');
                }

                //插入
                if ($day == 20181209) {
                    if ($tkData['nw_firm_id'] == 0) {
                        continue;
                    }
                    $reportUnitData['request'] = floor(rand(95, 105) * $tkData['request'] / 100);
                    $reportUnitData['filled_request'] = floor(rand(95, 105) * $tkData['filled_request'] / 100);
                    $reportUnitData['impression'] = floor(rand(95, 105) * $tkData['impression'] / 100);
                    $reportUnitData['click'] = floor(rand(95, 105) * $tkData['click'] / 100);
                    $reportUnitData['date_time'] = $tkData['date_time'];
                    $reportUnitData['nw_firm_id'] = $tkData['nw_firm_id'];
                    $reportUnitData['geo_short'] = $tkData['geo_short'];
                    $reportUnitData['publisher_id'] = $tkData['publisher_id'];
                    $reportUnitData['app_id'] = $tkData['app_id'];
                    $reportUnitData['placement_id'] = $tkData['placement_id'];
                    $reportUnitData['unit_id'] = $tkData['unit_id'];
                    $reportUnitData['format'] = $tkData['format'];
                    $reportUnitData['update_time'] = time();
                    $reportUnitData['revenue'] = rand(1, 2);
                    $post = $queryReportUnitDemoModel->insert($reportUnitData);
                    if ($post) {
                        Log::info('insert ReportUnitDemoModel request data succ');
                    }
                }
            }
            $start = $start + $length;
        }

        //追加report_unit数据
        if ($day == 20181208) {
            $list = $reportUnitModel->get([], $where);
            foreach ($list as $k => $v) {
                $tmpData = $v;
                unset($tmpData["date_time"]);
                unset($tmpData["id"]);
                $tmpData['date_time'] = $day;
                $post = $queryReportUnitDemoModel->insert($tmpData);
                if ($post) {
                    Log::info('insert ReportUnitDemoModel request data succ');
                }
            }
        }


        //report_dau_app
        $whereAppId = [$appId1, $appId2];
        $queryReportDauAppModel = $reportDauAppModel->queryBuilder();
        $queryReportDauAppDemoModel = $reportDauAppDemoModel->queryBuilder();
        if ($day == '20181208' || $day == '20181209') {
            $count = $queryReportDauAppModel->where('date_time', $yesterday)->whereIn('app_id', $whereAppId)->count();
        } else {
            $count = $queryReportDauAppDemoModel->where('date_time', $yesterday)->whereIn('app_id', $whereAppId)->count();
        }
        $start = 0;
        $length = 100;

        while ($start < $count) {
            if ($day == '20181208' || $day == '20181209') {
                $list = $queryReportDauAppModel->where('date_time', $yesterday)->whereIn('app_id', $whereAppId)->skip($start)->limit($length)->get();
            } else {
                $list = $queryReportDauAppDemoModel->where('date_time', $yesterday)->whereIn('app_id', $whereAppId)->skip($start)->limit($length)->get();
            }
            foreach ($list as $k => $v) {
                $tmpData = $v;
                unset($tmpData["date_time"]);
                unset($tmpData["id"]);
                $tmpData['date_time'] = $day;
                if ($day == '20181208') {
                    $tmpData['dau'] = $tmpData['dau'];
                } else if ($day == '20181209') {
                    $tmpData['dau'] = $tmpData['dau'] * 100;
                } else {
                    if ($type == "-") {
                        $tmpData['dau'] = $tmpData['dau'] - floor(rand(0, 10) * $tmpData['dau'] / 100);
                    } else {
                        $tmpData['dau'] = $tmpData['dau'] + floor(rand(0, 10) * $tmpData['dau'] / 100);
                    }
                }
                $post = $queryReportDauAppDemoModel->insert($tmpData);
                if ($post) {
                    Log::info('insert ReportDauAppDemoModel request data succ');
                }
            }
            $start = $start + $length;
        }

        //report_estimate
        if ($day == '20181208' || $day == '20181209') {
            $count = $reportEstimateModel->getCount($where);
        } else {
            $count = $reportEstimateDemoModel->getCount($where);
        }
        $start = 0;
        $length = 100;
        while ($start < $count) {
            if ($day == '20181208' || $day == '20181209') {
                $list = $reportEstimateModel->getBatch([], $where, $start, $length);
            } else {
                $list = $reportEstimateDemoModel->getBatch([], $where, $start, $length);
            }

            foreach ($list as $k => $v) {
                $tmpData = $v;
                unset($tmpData["date_time"]);
                unset($tmpData["id"]);
                $tmpData['date_time'] = $day;
                if ($day == '20181208') {
                    $tmpData['revenue'] = $tmpData['revenue'];
                } else if ($day == '20181209') {
                    $tmpData['revenue'] = $tmpData['revenue'] * 100;
                } else {
                    $incr = rand(0, 10) * $tmpData['revenue'] / 100;
                    $incr = round($incr, 2);
                    if ($type == "-") {
                        $tmpData['revenue'] = $tmpData['revenue'] - $incr;
                    } else {

                        $tmpData['revenue'] = $tmpData['revenue'] + $incr;
                    }
                }

                $post = $queryReportEstimateDemoModel->insert($tmpData);
                if ($post) {
                    Log::info('insert ReportEstimateDemoModel request data succ');
                }
            }
            $start = $start + $length;
        }

        if ($day <= 20181209) {
            return;
        }

        //report_unit
        $count = $reportUnitDemoModel->getCount($where);
        $start = 0;
        $length = 100;
        while ($start < $count) {
            $list = $reportUnitDemoModel->getBatch([], $where, $start, $length);
            foreach ($list as $k => $v) {
                $tmpData = $v;
                unset($tmpData["date_time"]);
                unset($tmpData["id"]);
                $tmpData['date_time'] = $day;
                $t5 = (rand(0, 10) * $tmpData['revenue'] / 100);
                if ($type == "-") {
                    $tmpData['request'] = $tmpData['request'] - floor((rand(0, 10) * $tmpData['request'] / 100));
                    $tmpData['filled_request'] = $tmpData['filled_request'] - floor(rand(0, 10) * $tmpData['filled_request'] / 100);
                    $tmpData['impression'] = $tmpData['impression'] - floor(rand(0, 10) * $tmpData['impression'] / 100);
                    $tmpData['click'] = $tmpData['click'] - floor(rand(0, 10) * $tmpData['click'] / 100);
                    $tmpData['revenue'] = $tmpData['revenue'] - round($t5, 2);
                } else {
                    $tmpData['request'] = $tmpData['request'] + floor(rand(0, 10) * $tmpData['request'] / 100);
                    $tmpData['filled_request'] = $tmpData['filled_request'] + floor(rand(0, 10) * $tmpData['filled_request'] / 100);
                    $tmpData['impression'] = $tmpData['impression'] + floor(rand(0, 10) * $tmpData['impression'] / 100);
                    $tmpData['click'] = $tmpData['click'] + floor(rand(0, 10) * $tmpData['click'] / 100);
                    $tmpData['revenue'] = $tmpData['revenue'] + round($t5, 2);
                }
                $post = $queryReportUnitDemoModel->insert($tmpData);
                if ($post) {
                    Log::info('insert ReportUnitDemoModel request data succ');
                }
            }
            $start = $start + $length;
        }


    }
}