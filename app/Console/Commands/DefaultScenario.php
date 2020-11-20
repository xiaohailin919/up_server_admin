<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MySql\Placement;
use App\Models\MySql\Scenario;

class DefaultScenario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:default-scenario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '补全Placement历史数据的默认场景';

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
        $placement = Placement::from(Placement::TABLE . ' as p')
            ->leftJoin(Scenario::TABLE . ' as s', 's.placement_id', '=', 'p.id')
            ->select(
                'p.id           as placement_id',
                'p.app_id       as app_id',
                'p.publisher_id as publisher_id',
                's.id           as scenario_id'
            )
            ->whereRaw('s.id is null')
            ->orderBy('p.id', 'asc')
            ->limit(10000)
            ->get();

        $this->info('start');
        foreach($placement as $val){
            $check = Scenario::where('publisher_id', $val['publisher_id'])
                ->where('app_id', $val['app_id'])
                ->where('placement_id', $val['placement_id'])
                ->where('uuid', 1)
                ->count();
            if($check > 0){
                continue;
            }
            $dateTime = date('Y-m-d H:i:s');
            $data = [
                'uuid'         => '1',
                'publisher_id' => $val['publisher_id'],
                'app_id'       => $val['app_id'],
                'placement_id' => $val['placement_id'],
                'name'         => 'Default',
                'remark'       => '',
                'status'       => 1,
                'create_time'  => $dateTime,
                'update_time'  => $dateTime,
            ];
            Scenario::create($data);
            $this->info("{$val['publisher_id']} > {$val['app_id']} > {$val['placement_id']}");
        }
        $this->info('done.');
    }
}
