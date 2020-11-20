<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Models\MySql\Unit;
use App\Models\MySql\MediationConnectionUnit;

class UnitSegment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:unit-segment {publisherId=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导出同个广告源用在多个Segment的情况';

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
        if($publisherId <= 0){
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
                'u.remote_unit as auth_content'
            )
            ->where('u.publisher_id', $publisherId)
            ->where('u.status', Unit::STATUS_RUNNING)
//            ->limit(5)
            ->get();
        $str = '"Publisher ID","App ID","App Name","Placement ID","Placement Name","Network Name","AD Source ID","AD Source Name","AD Source Token","AB Test ID","AB Test Name","Segment ID","Segment Name"';
        Storage::append('unit-segment.csv', $str);
        foreach($unit as $val){
            $tmp = $val;

            if(MediationConnectionUnit::where('unit_id', $val['id'])->count() <= 1){
                continue;
            }

            $authContent = (array)json_decode($tmp['auth_content']);
            $authContentString = '';
            foreach($authContent as $k => $v){
                $authContentString .= "{$k}: {$v}; ";
            }
            $tmp['auth_content'] = $authContentString;

            $mcUnit = MediationConnectionUnit::from('mediation_connection_unit as mcu')
                ->leftJoin('mediation_connection as mc', 'mc.id', '=', 'mcu.mediation_connection_id')
                ->leftJoin('traffic_group_ab_test as tg', 'tg.id', '=', 'mc.traffic_group_id')
                ->leftJoin('segment as s', 's.id', '=', 'mc.segment_id')
                ->select(
                    'tg.id as traffic_group_id',
                    'tg.name as traffic_group_name',
                    's.id as segment_id',
                    's.name as segment_name'
                )
                ->where('unit_id', $val['id'])
                ->get();
            foreach($mcUnit as $ke => $va){
                $tmp['traffic_group_id'] = $va['traffic_group_id'];
                $tmp['traffic_group_name'] = $va['traffic_group_name'];
                $tmp['segment_id'] = empty($va['segment_id']) ? '0' : $va['segment_id'];
                $tmp['segment_name'] = empty($va['segment_name']) ? 'Default' : $va['segment_name'];

                $str = '"' . implode('","', $tmp) . '"';
                Storage::append('unit-segment.csv', $str);
            }
        }
    }
}
