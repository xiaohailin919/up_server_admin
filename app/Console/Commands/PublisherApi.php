<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MySql\Publisher;
use App\Models\MySql\PublisherApiInfo;

use App\Utils\SHAHasher;

class PublisherApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:publisher-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动开启Publisher的Open API权限';

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
        $publishers = Publisher::from('publisher as p')
            ->leftJoin('publisher_api_info as a', 'a.publisher_id', '=', 'p.id')
            ->select(['p.id as id', 'p.mode as mode'])
            ->whereRaw('a.id is null')
            ->limit(500)
            ->get();

        $this->info('Start');
        foreach($publishers as $publisher){
            $this->info('Publisher ID: ' . $publisher['id']);

            $publisherApiInfoModel = new PublisherApiInfo();
            $apiRecord = $publisherApiInfoModel->where("publisher_id", $publisher['id'])
                ->first();
            if(empty($apiRecord)){
                $publisherKey = (new SHAHasher())->make(microtime().mt_rand());
                $data = [
                    'publisher_id'         => $publisher['id'],
                    'publisher_key'        => $publisherKey,
                    'up_api_permission'    => $publisher['mode'] == Publisher::MODE_BLACK ? PublisherApiInfo::UP_API_PERMISSION_OFF : PublisherApiInfo::UP_API_PERMISSION_ON,
                    'up_device_permission' => PublisherApiInfo::UP_DEVICE_PERMISSION_OFF,
                    'update_time'          => date('Y-m-d H:i:s'),
                    'create_time'          => date('Y-m-d H:i:s'),
                ];
                $publisherApiInfoModel->create($data);
            }
        }
        $this->info('Done');
    }
}
