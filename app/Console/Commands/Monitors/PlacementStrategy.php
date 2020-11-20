<?php

namespace App\Console\Commands\Monitors;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\MySql\App as AppModel;
use App\Models\MySql\Placement as PlacementModel;
use App\Models\MySql\StrategyPlacement as StrategyPlacementModel;

class PlacementStrategy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:placement-strategy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监控Placement策略是否存在数据缺失并修复';

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
        $data = PlacementModel::query()
            ->from(PlacementModel::TABLE . ' as p')
            ->leftJoin(StrategyPlacementModel::TABLE . ' as sp', 'sp.placement_id', '=', 'p.id')
            ->select('p.id', 'p.uuid', 'p.name', 'p.publisher_id')
            ->whereRaw('sp.id is null')
            ->orderBy('p.id', 'asc')
            ->limit(10)
            ->get();
        foreach($data as $val){
            $this->info($val);
            $strategy = [
                'placement_id'           => $val['id'],
                'wifi_autoplay'          => StrategyPlacementModel::WIFI_AUTO_PLAY_ON,
                'request_auto'           => 0,
                'nw_cache_time'          => 30 * 60,
                'nw_timeout'             => 5,
                'strategy_cache_timeout' => 0,
                'create_time'            => time(),
                'update_time'            => time(),
                'delivery'               => 1,
                'click_address'          => 'https://tt.toponad.com/v2/tk',
                'status'                 => StrategyPlacementModel::STATUS_RUNNING
            ];
            /* 对 Rewarded Video 或 Interstitial 类型广告位重新设置 network 超时时间 */
            $specialFormat = [PlacementModel::FORMAT_RV, PlacementModel::FORMAT_INTERSTITIAL];
            if (in_array($val['format'], $specialFormat, false)) {
                $platform = AppModel::query()
                    ->where('id', $val['app_id'])
                    ->value('platform');
                $strategy['nw_timeout'] = (int)$platform === AppModel::PLATFORM_ANDROID ? 12 : 8;
            }
            StrategyPlacementModel::query()->insertGetId($strategy);
            Log::error('Placement strategy fixed', (array)$val);
        }
        $this->info('done.');
    }
}
