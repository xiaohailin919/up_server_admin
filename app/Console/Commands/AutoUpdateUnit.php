<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MySql\Unit as UnitModel;
use App\Models\MySql\MediationRelationship as MediationRelationshipModel;

/**
 * Class AutoUpdateUnit
 * @package App\Console\Commands
 * @deprecated
 */
class AutoUpdateUnit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:update_unit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Command description';

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
        $lastTime = 2145888000;
        $todayTime = strtotime(date('Y-m-d 23:59:59'));
        $unitModel = new UnitModel();
        $mrsModel = new MediationRelationshipModel();
        /**
         * 赶时间做百度聚合技术设计
         * 跟洪炎确认先不错分页，先全部查出来做判断
         * 2018.11.30 上午
         */
        $unit = $unitModel->queryBuilder()
//            ->where('status', '!=', UnitModel::STATUS_RUNNING)
            ->orderBy('update_time', 'desc')
            ->limit(10000)
            ->get();

        foreach($unit as $val){

            // 检测关系表是否有数据
            $check = $mrsModel->queryBuilder()
                ->where('unit_id', $val['id'])
                ->count();
            
            if($val['crawl_last_time'] > $todayTime && ($val['status'] != UnitModel::STATUS_RUNNING || $check <= 0)){
                $unitModel->queryBuilder()
                    ->where('id', $val['id'])
                    ->update(['crawl_last_time' => $todayTime]);
            }else if($val['status'] == UnitModel::STATUS_RUNNING && $check > 0){
                $unitModel->queryBuilder()
                    ->where('id', $val['id'])
                    ->update(['crawl_last_time' => $lastTime]);

            }
            // 检查关系
//            if($check > 0){
//                $unitModel->queryBuilder()
//                    ->where('id', $val['id'])
//                    ->update(['crawl_last_time' => $lastTime]);
//            }else{
//                $unitModel->queryBuilder()
//                    ->where('id', $val['id'])
//                    ->update(['crawl_last_time' => $todayTime]);
//            }
        }
    }
}
