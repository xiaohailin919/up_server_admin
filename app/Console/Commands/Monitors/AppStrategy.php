<?php

namespace App\Console\Commands\Monitors;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\MySql\App;
use App\Models\MySql\StrategyApp;

class AppStrategy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:app-strategy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监控App策略是否存在数据缺失并修复';

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
        $this->info('start.');
        $data = App::query()
            ->from(App::TABLE . ' as a')
            ->leftJoin(StrategyApp::TABLE . ' as sa', 'sa.app_id', '=', 'a.id')
            ->select('a.id', 'a.uuid', 'a.name', 'a.publisher_id')
            ->whereRaw('sa.id is null')
            ->orderBy('a.id', 'asc')
            ->limit(10)
            ->get();
        foreach($data as $val){
            $this->info($val);
            $strategy = [
                'app_id'            => $val['id'],
                'cache_time'        => 60 * 60,
                'placement_timeout' => 5,
                'collect_material'  => 0,
                'collect_app_list'  => 0,
                'gdpr_consent_set'  => 0,
                'gdpr_server_only'  => 0,
                'gdpr_notify_url'   => 'https://img.anythinktech.com/gdpr/PrivacyPolicySetting.html',
                'create_time'       => time(),
                'update_time'       => time(),
                'status'            => 2,
            ];
            StrategyApp::query()->insert($strategy);
            Log::error('App strategy fixed', (array)$val);
        }
        $this->info('done.');
    }
}
