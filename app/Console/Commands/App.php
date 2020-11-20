<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\Queue;
use App\Models\MySql\App as AppMyModel;
use App\Services\App as AppService;

class App extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:app {mode=latest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync APP data to MongoDB';

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

        $data = $queueModel->getAppWaiting();

        foreach($data as $val){
            AppService::queueSync($val['id'], $val['object_id']);
        }
    }
    
    /**
     * 全量同步
     */
    private function syncAll()
    {
        $appMyModel = new AppMyModel();

        $field = [
            'id as app_id',
            'uuid as app_uuid',
            'publisher_id',
            'name',
            'status'
        ];

        $count = $appMyModel->getCount();
        $start = 0;
        $length = 1000;

        while ($start <= $count) {
             
            $data = $appMyModel->getBatch($field, [], $start, $length);
            
            $start += $length;
            foreach($data as $app){
                AppService::sync($app['app_id']);
            }
        }
    }
}
