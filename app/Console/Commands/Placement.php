<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\Queue;
use App\Models\MySql\Placement as PlacementMyModel;
use App\Services\Placement as PlacementService;

/**
 * Class Placement
 * @package App\Console\Commands
 * @deprecated
 */
class Placement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:placement {mode=latest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Sync Placement data to MongoDB';

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
        $this->info('Go away！不在这里同步了。');
        return true;

        $mode = $this->argument('mode');
        if($mode == 'all'){
            $this->syncAll();
        }else{
            $this->syncLatest();
        }
    }
    
    /**
     * 增量同步
     */
    private function syncLatest()
    {
        $queueModel = new Queue();
        
        $data = $queueModel->getPlacementWaiting();
        
        foreach($data as $val){
            PlacementService::queueSync($val['id'], $val['object_id']);
        }
    }
    
    /**
     * 全量同步
     */
    private function syncAll()
    {
        $placementMyModel = new PlacementMyModel();

        $field = [
            'id as placement_id',
            'uuid as placement_uuid',
            'publisher_id',
            'app_id',
            'name',
            'status'
        ];
        $count = $placementMyModel->getCount();
        $start = 0;
        $length = 1000;

        while ($start <= $count) {
             
            $data = $placementMyModel->getBatch($field, [], $start, $length);

            $start += $length;
            foreach($data as $val){
                PlacementService::sync($val['placement_id']);
            }
        
        }

    }
}
