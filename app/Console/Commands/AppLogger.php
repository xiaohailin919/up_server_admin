<?php

namespace App\Console\Commands;

use App\Models\MySql\StrategyAppLogger;
use Illuminate\Console\Command;

class AppLogger extends Command
{
    protected $signature = 'auto:app-logger';

    protected $description = '复制 StrategyAppLogger 中旧字段的数据到新字段中';

    public function handle()
    {
        echo "Operate on: strategy_app_logger\nCurrent Time: " . date('Y-m-d H:i:s');
        $strategyAppLoggers = StrategyAppLogger::query()->get();
        $len = count($strategyAppLoggers);
        echo 'Operations: ' . $len . "\n";
        for ($i = 0; $i < $len; $i++) {
            if ($i % 10 === 0) {
                echo "Progress: " . $i / $len . "\n";
            }
            $daNotKeysFormats = json_decode($strategyAppLoggers[$i]['da_not_keys_ft'], true);
            $tkNotKeysFormats = json_decode($strategyAppLoggers[$i]['tk_no_t_ft'], true);
            if (count($daNotKeysFormats) > 0) {
                $daNotKeysFormatsRates = json_decode($strategyAppLoggers[$i]['da_no_report_keys_by_rate'], true);
                foreach ($daNotKeysFormats as $daNotKeys => $formats) {
                    /* 如果设置了，以最新的为标准 */
                    if (!isset($daNotKeysFormatsRates[$daNotKeys])) {
                        $daNotKeysFormatsRates[$daNotKeys] = [
                            'formats' => $formats,
                            'rate' => 0,
                        ];
                    }
                }
                $strategyAppLoggers[$i]['da_no_report_keys_by_rate'] = json_encode($daNotKeysFormatsRates);
            } else {
                $strategyAppLoggers[$i]['da_no_report_keys_by_rate'] = json_encode([]);
            }
            if (count($tkNotKeysFormats) > 0) {
                $tkNotKeysFormatsRates = json_decode($strategyAppLoggers[$i]['tk_no_report_keys_by_rate'], true);
                foreach ($tkNotKeysFormats as $tkNotKeys => $formats) {
                    /* 如果设置了，以最新的为标准 */
                    if (!isset($tkNotKeysFormatsRates[$tkNotKeys])) {
                        $tkNotKeysFormatsRates[$tkNotKeys] = [
                            'formats' => $formats,
                            'rate' => 0,
                        ];
                    }
                }
                $strategyAppLoggers[$i]['tk_no_report_keys_by_rate'] = json_encode($tkNotKeysFormatsRates);
            } else {
                $strategyAppLoggers[$i]['tk_no_report_keys_by_rate'] = json_encode([]);
            }
            $strategyAppLoggers[$i]->save();
        }
        echo "Job finish.\n";
    }
}
