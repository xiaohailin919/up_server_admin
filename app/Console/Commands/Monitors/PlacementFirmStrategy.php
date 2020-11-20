<?php

namespace App\Console\Commands\Monitors;

use Illuminate\Console\Command;

use App\Models\MySql\App as AppModel;
use App\Models\MySql\Placement as PlacementModel;
use App\Models\MySql\StrategyPlacement;
use App\Models\MySql\StrategyPlacementFirm;

class PlacementFirmStrategy extends Command
{
    protected $signature = 'monitor:placement-firm-strategy {limit=1000}';

    protected $description = 'Add placement firm strategies for specific format placements. Example: monitor:placement-firm-strategy 1000';

//    /**
//     * @var array 广告类型所对应的需要追加 PlacementFirm 策略的厂商配置
//     * 可以套在 SQL 中，暂不使用
//     */
//    private $formatNwFirmCfg = [
//        PlacementModel::FORMAT_NETIVE       => [],
//        PlacementModel::FORMAT_RV           => [35, 36, 37],
//        PlacementModel::FORMAT_BANNER       => [37],
//        PlacementModel::FORMAT_INTERSTITIAL => [35, 36, 37],
//        PlacementModel::FORMAT_SPLASH       => []
//    ];

    /**
     * @var array 厂商所对应的默认 nw_cache_time 配置
     */
    private $nwCacheTimeSpecCfg = [
        5  => 1800,     6  => 25200,    8  => 1800,
        14 => 1800,     20 => 960,      25 => 1800,
        26 => 1800,     27 => 1800,     30 => 1800,
        31 => 1800,     32 => 1800,     33 => 1800,
        34 => 1800,     35 => 86400,    37 => 1200,
    ];

    public function handle()
    {
        $limit = $this->argument('limit');

        /*
        SELECT `sp`.`id` AS `ap_id`, `p`.`id` AS `p_id`, `app`.`platform`, `p`.`format`, `nf`.`id` AS `nw_firm_id`, `spf`.`id` AS `spf_id`
            FROM `strategy_placement` AS `sp`
            LEFT JOIN `placement` AS `p` ON `sp`.`placement_id` = `p`.`id`
            LEFT JOIN `app` ON `app`.`id` = `p`.`app_id`
            CROSS JOIN `network_firm` AS `nf`
            LEFT JOIN `strategy_placement_firm` AS `spf` ON `spf`.`str_placement_id` = `sp`.`id` AND `spf`.`nw_firm_id` = `nf`.`id`
        WHERE `p`.`system` = 1
          AND spf.`id` IS NULL
          AND (
            (`nf`.`id` IN (35, 36, 37) AND `p`.`format` IN (1, 3)) OR (`nf`.`id` = 37 AND `p`.`format` = 2)
          )
        LIMIT 1000;
         */
        $query = StrategyPlacement::query()
            ->from('strategy_placement as sp')
            ->crossJoin('network_firm as nf')
            ->leftJoin('placement as p', 'sp.placement_id', '=', 'p.id')
            ->leftJoin('app', 'app.id', '=', 'p.app_id')
            ->leftJoin('strategy_placement_firm as spf', static function ($join) {
                $join->on('spf.str_placement_id', '=', 'sp.id')->on('spf.nw_firm_id', '=', 'nf.id');
            })
            ->select('sp.id as sp_id', 'p.id as p_id', 'app.platform', 'p.format', 'nf.id as nw_firm_id', 'spf.id as spf_id')
            ->where('p.system', '=', '1')
            ->whereNull('spf.id')
            ->where(static function ($query) {
                /* 对应上文 $formatNwFirmCfg 配置，暂不使用 */
                $query->where(static function ($innerQuery) {
                    $innerQuery->whereIn('nf.id', [35, 36, 37])
                        ->whereIn('p.format', [PlacementModel::FORMAT_RV, PlacementModel::FORMAT_INTERSTITIAL]);
                })->orWhere(static function ($innerQuery) {
                    $innerQuery->where('nf.id', '=', '37')
                        ->where('p.format', PlacementModel::FORMAT_BANNER);
                });
            });

        $lackingRecordsCount = $query->count();
        /* { "sp_id": -, "p_id": -, "platform": -, "format": -, "nw_firm_id": -, "spf_id": NULL} */
        $data = $query->limit($limit)->get();

        $this->info('All Placement Firm Strategies needs to be created: ' . $lackingRecordsCount
            . "\nNow to be created: " . count($data));

        $newRecords = [];
        foreach ($data as $datum) {
            $newRecord = [
                'str_placement_id'   => $datum['sp_id'],
                'nw_firm_id'         => $datum['nw_firm_id'],
                'nw_cache_time'      => array_key_exists($datum['nw_firm_id'], $this->nwCacheTimeSpecCfg) ? $this->nwCacheTimeSpecCfg[$datum['nw_firm_id']] : 3600,
                'nw_timeout'         => $datum['platform'] === AppModel::PLATFORM_ANDROID ? 12 : 8,
                'ad_data_nw_timeout' => -1,
                'nw_offer_requests'  => 1,
                'create_time'        => time(),
                'update_time'        => time(),
                'status'             => 2,
            ];
            $newRecords[] = $newRecord;
        }
        StrategyPlacementFirm::query()->insert($newRecords);

        $this->info(count($newRecords) . ' placement firm strategies created by batch');
    }
}
