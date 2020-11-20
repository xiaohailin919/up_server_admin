<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\Queue;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\Mongo\Publisher as PublisherMoModel;
use App\Services\Publisher as PublisherService;

class Publisher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:publisher {mode=latest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Publisher data to MongoDB';

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

        $data = $queueModel->getPublisherWaiting();

        foreach ($data as $val) {
            PublisherService::queueSync($val['id'], $val['object_id']);
        }
    }
    
    /**
     * 全量同步
     */
    private function syncAll()
    {
        $publisherMyModel = new PublisherMyModel();

        $field = [
            'id as publisher_id',
            'email',
            'api_key',
            'status'
        ];
        $data = $publisherMyModel->get($field, []);

        foreach ($data as $publisher) {
            PublisherService::sync($publisher['publisher_id']);
        }
    }
}
