<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\UGroup as UGroupMyModel;
use App\Models\MySql\MGroupRelationship as MGroupRsMyModel;
use App\Models\MySql\ReportUnit as ReportUnitMyModel;

/**
 * Class AutoOptEcpm
 * @package App\Console\Commands
 * @deprecated
 */
class AutoOptEcpm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:opt_ecpm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Auto Optimization ugroup eCPM.';

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
        // 取ugroup
        $ugroupMyModel = new UGroupMyModel;
        $ugroup = $ugroupMyModel->getOptLists();
        $reportUnitMyModel = new ReportUnitMyModel;
        $mgroupRsMyModel = new MGroupRsMyModel;
        foreach($ugroup as $ug){
            $unitIds = $mgroupRsMyModel->getUnitIdsByUgId($ug['id']);
            $report = $reportUnitMyModel->getReportByUnitId($unitIds);
            if(empty($report)){
                // 更新状态为分析中
                $ugroupMyModel->queryBuilder()
                        ->where('id', $ug['id'])
                        ->where('opt_switch', UGroupMyModel::OPT_SWITCH_ON)
                        ->update(['opt_status' => UGroupMyModel::OPT_STATUS_OPTIMIZING]);
                continue;
            }
            // 计算eCPM并更新状态为已优化
            $ecpm = $this->getEcpm($report);
            $data = [
                'ecpm' => $ecpm,
                'opt_status' => UGroupMyModel::OPT_STATUS_OPTIMIZED,
                'opt_time' => time()
            ];
            $ugroupMyModel->queryBuilder()
                        ->where('id', $ug['id'])
                        ->where('opt_switch', UGroupMyModel::OPT_SWITCH_ON)
                        ->update($data);
        }
    }
    
    /**
     * 获取优化后的eCPM
     * @param array $report
     * @return float
     */
    private function getEcpm($report)
    {
        if(!isset($report['impression']) || $report['impression'] <= 0){
            return 0.01;
        }
        $ecpm = round($report['revenue'] / $report['impression'] * 1000, 2);
        if($ecpm <= 0){
            $ecpm = 0.01;
        }
        return $ecpm;
    }
}
