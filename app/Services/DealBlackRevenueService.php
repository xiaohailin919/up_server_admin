<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2019/1/23
 * Time: 14:48
 */

namespace App\Services;

use App\Models\MySql\Placement as PlacementModel;
use App\Models\MySql\Publisher as PublisherModel;
use App\Models\MySql\ReportTk as ReportTkModel;
use App\Models\MySql\ReportUnit as ReportUnitModel;
use App\Models\MySql\ReportEstimate as ReportEstimateModel;
use App\Models\MySql\ReportDauApp as ReportDauAppModel;
use App\Models\MySql\ReportBlackAssignment as ReportBlackAssignmentModel;
use App\Models\MySql\ReportBlackIncome as ReportBlackIncomeModel;
use App\Models\MySql\ReportBlackAssignmentActualLog as ReportBlackAssignmentActualLogModel;


use App\Models\MySql\ReportUnitDemo as ReportUnitDemoModel;
use App\Models\MySql\ReportTkDemo as ReportTkDemoModel;
use App\Models\MySql\ReportEstimateDemo as ReportEstimateDemoModel;
use App\Models\MySql\ReportDauAppDemo as ReportDauAppDemoModel;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DealBlackRevenueService
{
    public static function toDealBlackRevenue($day, $user, $placement)
    {
        $publisherModel = new PublisherModel();
        $placementModel = new PlacementModel();
        $reportUnitModel = new ReportUnitModel();
        $reportBlackAssignmentModel = new ReportBlackAssignmentModel();
        $reportBlackAssignmentActualLogModel = new ReportBlackAssignmentActualLogModel();
        $reportBlackIncomeModel = new ReportBlackIncomeModel();
        $reportTkModel = new ReportTkModel();

        $publisherWhere = [];
        if (!empty($user)) {
            $publisherWhere = ['id' => $user];
        }

        $publisherBlackCount = $publisherModel->getCount($publisherWhere);
        $start = 0;
        $length = 1000;
        while ($start < $publisherBlackCount) {
            $publisherList = $publisherModel->getBatch([], $publisherWhere, $start, $length);
            $start = $start + $length;

            foreach ($publisherList as $k => $v) {
                $publisherId = $v['id'];
                if (!empty($placement)) {
                    $placementWhere = ['uuid' => $placement, 'publisher_id' => $publisherId];
                } else {
                    $placementWhere = ['publisher_id' => $publisherId];
                }
                $placementList = $placementModel->get([], $placementWhere);

                foreach ($placementList as $kk => $vv) {
                    $placementId = $vv['id'];
                    $onePlacement = $vv;
                    $apiRevenueList = $reportUnitModel->queryBuilder()
                        ->select('date_time', 'placement_id', 'format', 'geo_short', DB::raw('SUM(revenue) as revenue'))
                        ->where('date_time', $day)
                        ->where('placement_id', $placementId)
                        ->groupBy('date_time', 'placement_id', 'format', 'geo_short')
                        ->get();
                    $oneBlackAssignment = $reportBlackAssignmentModel->getOne([], ['placement_id' => $vv['id'], 'country' => '00', 'status' => 3]);
                    $tmpDiscountArr = [];

                    $logWhere = [
                        'placement_id' => $placementId,
                        'country' => '00',
                        'cday' => $day,
                    ];
                    $oneLog = $reportBlackAssignmentActualLogModel->getOne([], $logWhere);
                    if ($oneBlackAssignment['assignment_type'] == 1) {
                        if (empty($oneLog) && !empty($oneBlackAssignment)) {
                            $tmpDiscountArr = $reportBlackAssignmentModel->getDiscountActualValue($oneBlackAssignment);
                        }
                        if (!empty($oneLog)) {
                            $tOneLogValue = json_decode($oneLog['actual_log'], true);
                            $tmpDiscountArr1 = $reportBlackAssignmentModel->getDiscountActualValue($oneBlackAssignment, $tOneLogValue['random_range']);
                            if ($tmpDiscountArr1) {
                                $tmpDiscountArr['random'] = $tOneLogValue['random_range'];
                                $tmpDiscountArr['last'] = $tmpDiscountArr1['last'];
                            } else {
                                $tmpDiscountArr['random'] = $tOneLogValue['random_range'];
                                $tmpDiscountArr['last'] = $tOneLogValue['actual_value'];
                            }
                        }
                    } else {
                        if (empty($oneLog) && !empty($oneBlackAssignment)) {
                            $tmpDiscountArr = $reportBlackAssignmentModel->getEcpmActualValue($oneBlackAssignment);
                        }
                        if (!empty($oneLog)) {
                            $tOneLogValue = json_decode($oneLog['actual_log'], true);
                            $tmpDiscountArr1 = $reportBlackAssignmentModel->getEcpmActualValue($oneBlackAssignment, $tOneLogValue['random_range']);
                            if ($tmpDiscountArr1) {
                                $tmpDiscountArr['random'] = $tOneLogValue['random_range'];
                                $tmpDiscountArr['last'] = $tmpDiscountArr1['last'];
                            } else {
                                $tmpDiscountArr['random'] = $tOneLogValue['random_range'];
                                $tmpDiscountArr['last'] = $tOneLogValue['actual_value'];
                            }
                        }
                    }
                    $isDefault = false;
                    if (empty($oneBlackAssignment)) {
                        $oneBlackAssignment = $reportBlackAssignmentModel->getOne([], ['placement_id' => 0, 'country' => '00']);
                        $isDefault = true;
                    }
                    $upImpressions = $reportTkModel
                        ->queryBuilder()
                        ->where('date_time', $day)
                        ->where('placement_id', $placementId)
                        ->sum("impression");
                    $placementApiRevenueSum = $reportUnitModel
                        ->queryBuilder()
                        ->where('date_time', $day)
                        ->where('placement_id', $placementId)
                        ->sum("revenue");
                    foreach ($apiRevenueList as $kkk => $vvv) {
                        if ($vvv['revenue'] > 0) {
                            $apiRevenueSum = $vvv['revenue'];
                            if ($v['mode'] == 1) {
                                $tmpWhere = [
                                    'date_time' => $day,
                                    'publisher_id' => $publisherId,
                                    'placement_id' => $placementId,
                                    'country' => $vvv['geo_short'],
                                ];
                                self::addIncomeData($tmpWhere, $apiRevenueSum, $day, $publisherId, $onePlacement, [], [], $vvv);
                            } else {
                                $tmpWhere = [
                                    'date_time' => $day,
                                    'publisher_id' => $publisherId,
                                    'placement_id' => $placementId,
                                    'country' => $vvv['geo_short'],
                                ];
                                if ($isDefault) {
                                    $discount = $apiRevenueSum * $oneBlackAssignment['expected_value'] / 100;
                                    self::addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, [], [], $vvv);
                                } else {
                                    if ($oneBlackAssignment['assignment_type'] == 1) {
                                        if ($apiRevenueSum * ($tmpDiscountArr['last'] - 1) <= $oneBlackAssignment['maximum_compensation']) {
                                            $discount = $apiRevenueSum * $tmpDiscountArr['last'];
                                            self::addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, $oneBlackAssignment, $tmpDiscountArr, $vvv);
                                        } else {
                                            $tmpM = 1 + $oneBlackAssignment['maximum_compensation'] / $apiRevenueSum;
                                            $discount = $apiRevenueSum * $tmpM;
                                            self::addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, $oneBlackAssignment, $tmpDiscountArr, $vvv);
                                        }
                                    } elseif ($oneBlackAssignment['assignment_type'] == 2) {
                                        if ($upImpressions > 0) {
                                            $apiEcpm = $placementApiRevenueSum / $upImpressions * 1000;
                                            $discounts = $tmpDiscountArr['last'] / $apiEcpm;
                                        } else {
                                            $apiEcpm = 0;
                                            $discounts = 0;
                                        }
                                        if ($apiRevenueSum * ($discounts - 1) <= $oneBlackAssignment['maximum_compensation']) {
                                            $discount = $apiRevenueSum * $discounts;
                                            self::addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, $oneBlackAssignment, $tmpDiscountArr, $vvv);
                                        } else {
                                            $tmpM = 1 + $oneBlackAssignment['maximum_compensation'] / $apiRevenueSum;
                                            $discount = $apiRevenueSum * $tmpM;
                                            self::addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, $oneBlackAssignment, $tmpDiscountArr, $vvv);
                                        }

                                    }
                                }
                            }

                        }
                    }


                }

            }


        }


    }


    public static function addIncomeData($tmpWhere, $discount, $day, $publisherId, $onePlacement, $oneBlackAssignment = [], $tmpDiscountArr = [], $oneReportUnit = [])
    {
        $placementId = $onePlacement['id'];
        $appId = $onePlacement['app_id'];
        $reportBlackIncomeModel = new ReportBlackIncomeModel();
        $one = $reportBlackIncomeModel->getOne([], $tmpWhere);
        if ($one) {
            $revenueData = [
                'revenue' => $discount,
                'utime' => date('Y-m-d H:i:s'),
            ];
            $res = $reportBlackIncomeModel->queryBuilder()
                ->where('id', $one['id'])
                ->update($revenueData);
        } else {
            $revenueData = [
                'date_time' => $day,
                'app_id' => $appId,
                'publisher_id' => $publisherId,
                'placement_id' => $placementId,
                'country' => $oneReportUnit['geo_short'],
                'format' => $oneReportUnit['format'],
                'revenue' => $discount,
                'ctime' => date('Y-m-d H:i:s'),
                'utime' => date('Y-m-d H:i:s'),
            ];
            $res = $reportBlackIncomeModel->queryBuilder()->insert($revenueData);
        }
        if (!empty($tmpDiscountArr)) {
            $reportBlackAssignmentActualLogModel = new ReportBlackAssignmentActualLogModel();
            if ($oneBlackAssignment['assignment_type'] == 2) {
                $oneBlackAssignment['expected_value'] = $oneBlackAssignment['expected_value'] / 1000;
            }
            $actualLog = json_encode([
                'assignment_type' => $oneBlackAssignment['assignment_type'],
                'expected_value' => $oneBlackAssignment['expected_value'],
                'random_range' => $tmpDiscountArr['random'],
                'maximum_compensation' => $oneBlackAssignment['maximum_compensation'],
                'actual_value' => $tmpDiscountArr['last'],
            ]);
            $logWhere = [
                'placement_id' => $placementId,
                'country' => '00',
                'cday' => $day,
                'actual_log' => $actualLog,
            ];
            $oneLog = $reportBlackAssignmentActualLogModel->getOne([], $logWhere);
            if (!$oneLog) {
                $data = [
                    'placement_id' => $placementId,
                    'country' => '00',
                    'cday' => $day,
                    'actual_log' => $actualLog,
                    'ctime' => date("Y-m-d H:i:s"),
                    'utime' => date("Y-m-d H:i:s"),
                ];
                $reportBlackAssignmentActualLogModel->queryBuilder()->insert($data);
            }
        }
        if (empty($res)) {
            Log::error('reportBlackIncomeModel deal data error');
        }
    }


}