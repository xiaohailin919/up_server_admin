<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MySql\TrafficGroup;
use App\Models\MySql\MediationConnection as McModel;

class MediationConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:mediation-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '聚合管理高级设置历史数据迁移（mediation_connection表）';

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
        $this->info('Start');
        $this->info('Placement ID > Traffic Group ID');
        $trafficGroup = TrafficGroup::limit(15000)
            ->orderBy('placement_id')
            ->orderBy('id')
            ->get();
        foreach($trafficGroup as $val){
            $this->info("{$val['placement_id']} > {$val['id']}");

            $update = [
                'auto_refresh_switch' => $val['auto_refresh_switch'],
                'auto_refresh_time'   => $val['auto_refresh_time'],
                'cap_hour_switch'     => $val['cap_hour_switch'],
                'cap_hour'            => $val['cap_hour'],
                'cap_day_switch'      => $val['cap_day_switch'],
                'cap_day'             => $val['cap_day'],
                'pacing_switch'       => $val['pacing_switch'],
                'pacing'              => $val['pacing'],
            ];

            McModel::where('placement_id', $val['placement_id'])
                ->where('traffic_group_id', $val['id'])
                ->update($update);
        }
        $this->info('done.');
    }
}
