<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\MySql\Unit;
use App\Models\MySql\ReportUnit;
use App\Models\MySql\MediationConnectionUnit;

/**
 * Class CoverEcpm
 * @package App\Console\Commands
 * @deprecated
 */
class CoverEcpm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:cover-ecpm {publisherId=0} {startTime=0} {endTime=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! 为了使开发者尽快过渡到新版本开发者后台，需要将旧版本账号正在使用的广告源eCPM价格，用历史API数据进行批量覆盖';

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
        $publisherId = (int)$this->argument('publisherId');
        $startTime = (int)$this->argument('startTime');
        $endTime = (int)$this->argument('endTime');
        if($publisherId <= 0 || $startTime <= 0 || $endTime <= 0){
            return false;
        }
        $unitModel = new Unit();
        $unit = $unitModel->queryBuilder()
            ->from('unit as u')
            ->leftJoin('placement as p', 'p.id', '=', 'u.placement_id')
            ->leftJoin('app as a', 'a.id', '=', 'p.app_id')
            ->leftJoin('network as n', 'n.id', '=', 'u.network_id')
            ->leftJoin('network_firm as f', 'f.id', '=', 'n.nw_firm_id')
            ->select(
                'u.publisher_id as publisher_id',
                'a.uuid as app_id',
                'a.name as app_name',
                'p.uuid as placement_id',
                'p.name as placement_name',
                'f.name as network_name',
                'u.id as id',
                'u.name as name',
                'u.remote_unit as auth_content',
                'u.ecpm as ecpm'
            )
            ->where('u.publisher_id', $publisherId)
            ->where('u.status', Unit::STATUS_RUNNING)
//            ->limit(5)
            ->get();
        $str = '"Publisher ID","App ID","App Name","Placement ID","Placement Name","Network Name","AD Source ID","AD Source Name","AD Source Token","eCPM Price","Revenue","Impression API","eCPM API"';
        Storage::append('cover-ecpm.csv', $str);
        foreach($unit as $key => $val){
            $tmp = $val;

            if(MediationConnectionUnit::where('unit_id', $val['id'])->count() <= 0){
                continue;
            }

            $authContent = (array)json_decode($tmp['auth_content']);
            $authContentString = '';
            foreach($authContent as $k => $v){
                $authContentString .= "{$k}: {$v}; ";
            }
            $tmp['auth_content'] = $authContentString;
            $tmp['revenue'] = 0;
            $tmp['impression'] = 0;
            $tmp['ecpm_api'] = 0;
            $report = ReportUnit::selectRaw('sum(revenue) as revenue, sum(impression) as impression')
                ->where('unit_id', $val['id'])
                ->where('date_time', '>=', $startTime)
                ->where('date_time', '<=', $endTime)
//                ->where('impression', '>=', 1000)
                ->groupBy('unit_id')
                ->first();
            if($report){
                $tmp['revenue'] = $report->revenue;
                $tmp['impression'] = $report->impression;
                $tmp['ecpm_api'] = $report->impression > 0 ? round($report->revenue / $report->impression * 1000, 2) : 0;
                if($tmp['ecpm_api'] >= 0.01){
                    // 覆盖eCPM
//                    $unitModel->queryBuilder()
//                        ->where('publisher_id', $publisherId)
//                        ->where('id', $val['id'])
//                        ->update(['ecpm' => $tmp['ecpm_api']]);
                }else{
                    $tmp['ecpm_api'] = 0.01;
                }
                if($report->impression < 100){
                    $tmp['ecpm_api'] = 0.01;
                }
                // 覆盖eCPM
                $unitModel->queryBuilder()
                    ->where('publisher_id', $publisherId)
                    ->where('id', $val['id'])
                    ->update([
                        'ecpm' => $tmp['ecpm_api'],
                        'update_time' => time()
                    ]);
            }
            $str = '"' . implode('","', $tmp) . '"';
            Storage::append('cover-ecpm.csv', $str);
        }
    }
}
