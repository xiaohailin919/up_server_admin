<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\Unit as UnitMyModel;
use App\Models\MySql\NetworkFirm as NetworkFirmMyModel;
use App\Models\MySql\Network as NetworkMyModel;
use App\Models\MySql\MGroupRelationship as MgRelationshipMyModel;
use App\Models\MySql\UGroup as UGroupMyModel;
use App\Models\MySql\ReportTk as ReportTkMyModel;
use App\Models\MySql\ReportEstimate as ReportEstimateMyModel;

/**
 * Class AutoEstRevenue
 * @package App\Console\Commands
 * @deprecated
 */
class AutoEstRevenue extends Command
{
    private $dateTime = 0;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:est_revenue {mode=network}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Auto sstimate placement revenue.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mode = $this->argument('mode');
        $this->dateTime = date('Ymd', strtotime('-1 day'));
        if($mode == 'network'){
            $this->networkNoApi();
        }else{
            $this->unitConfigError();
        }
    }
    
    /**
     * 不支持报表抓取的Network
     */
    public function networkNoApi()
    {
        // 获取不支持抓取报表的Network厂商
        $networkFirmMyModel = new NetworkFirmMyModel();
        $networkFirm = $networkFirmMyModel->newQuery()
                ->where('crawl_support', $networkFirmMyModel::CRAWL_SUPPORT_DAY_NO)
                ->get();
        $nwFirmId = [];
        foreach($networkFirm as $val){
            $nwFirmId[] = $val['id'];
        }
        // 获取Netowrk
        $networkMyModel = new NetworkMyModel();
        $network = $networkMyModel->queryBuilder()
                ->whereIn('nw_firm_id', $nwFirmId)
                ->where('status', '>', $networkMyModel::STATUS_DELETED)
                ->get();
        $networkId = [];
        foreach($network as $val){
            $networkId[] = $val['id'];
        }
        // 获取Unit
        $unitMyModel = new UnitMyModel();
        $unit = $unitMyModel->queryBuilder()
                ->whereIn('network_id', $networkId)
                ->where('status', '>', $unitMyModel::DELIVERY_STOP)
                ->get();
        $processed = [];
        foreach($unit as $val){
            $placementId = $val['placement_id'];
            $unitId = $val['id'];
            $firmId = $unitMyModel->getFirmIdById($unitId);
            $processKey = "{$placementId}-{$firmId}";
            if(in_array($processKey, $processed)){
                continue;
            }
            $this->placementEstRevenue($placementId, $firmId);
            $processed[] = $processKey;
        }
    }
    
    /**
     * 报表抓取信息配置出错的Unit
     */
    public function unitConfigError()
    {
        $unitMyModel = new UnitMyModel();
        $unit = $unitMyModel->queryBuilder()
                ->where('status', '>', $unitMyModel::DELIVERY_STOP)
                ->where('crawl_status', $unitMyModel::CRAWL_STATUS_FAILURE)
                ->get();
        $processed = [];
        foreach($unit as $val){
            $placementId = $val['placement_id'];
            $unitId = $val['id'];
            $firmId = $unitMyModel->getFirmIdById($unitId);
            $processKey = "{$placementId}-{$firmId}";
            if(in_array($processKey, $processed)){
                continue;
            }
            $this->placementEstRevenue($placementId, $firmId);
            $processed[] = $processKey;
        }
    }
    
    /**
     * 对单个placement计算预估收入
     * @param int $placementId
     */
    private function placementEstRevenue($placementId, $firmId)
    {
        if($firmId <= 0){
            return false;
        }
        
        $reportTkMyModel = new ReportTkMyModel();
        $tk = $reportTkMyModel->queryBuilder()
                ->select('date_time', 'group_id', 'nw_firm_id', 'geo_short', 'publisher_id', 'app_id', 'placement_id', 'format', 'impression')
                ->where('date_time', $this->dateTime)
                ->where('placement_id', $placementId)
                ->where('nw_firm_id', $firmId)
                ->get();
        
        $reportEstimateMyModel = new ReportEstimateMyModel();
        $relationshipMyModel = new MgRelationshipMyModel();
        foreach($tk as $val){
            $relationship = $relationshipMyModel->getByPlacementIdOrderByEcpm($placementId, $val['group_id'], $firmId);
            if(empty($relationship)){
                continue;
            }
            $ecpm = $relationship['ecpm'];
            $revenue = round($ecpm * $val['impression'] / 1000, 2);
            $data = [
                'date_time' => $this->dateTime,
                'group_id' => $val['group_id'],
                'nw_firm_id' => $val['nw_firm_id'],
                'geo_short' => $val['geo_short'],
                'publisher_id' => $val['publisher_id'],
                'app_id' => $val['app_id'],
                'placement_id' => $val['placement_id'],
                'format' => $val['format'],
                'revenue' => $revenue,
                'revenue_status' => 1,
                'create_time' => time(),
            ];
            $check = $reportEstimateMyModel->queryBuilder()
                    ->where('date_time', $this->dateTime)
                    ->where('group_id', $val['group_id'])
                    ->where('nw_firm_id', $val['nw_firm_id'])
                    ->where('geo_short', $val['geo_short'])
                    ->where('placement_id', $val['placement_id'])
                    ->first();
            if($check){
                $reportEstimateMyModel->queryBuilder()->where('id', $check['id'])->delete();
            }
            $reportEstimateMyModel->queryBuilder()->insert($data);
        }
    }
}
