<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MySql\App;
use App\Helpers\Utils;

class AppStoreId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:app-store-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提取AppStoreId';

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
        $apps = App::where('platform', 2)
            ->where('store_url', '!=', '')
            ->where('app_store_id', '')
            ->limit(1000)
            ->get()
            ->toArray();

        foreach($apps as $app){
            $appStoreId = Utils::getAppStoreId($app['store_url']);
            if(!empty($appStoreId) && is_numeric($appStoreId)){
                App::where('id', $app['id'])
                    ->update(['app_store_id' => $appStoreId]);
                $this->info("ID: {$app['id']}; AppStoreId: {$appStoreId}");
            }
        }
        $this->info('done.');
    }
}
