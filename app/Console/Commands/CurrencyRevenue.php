<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\MySql\Unit;
use App\Models\MySql\Publisher as PublisherModel;
use App\Models\MySql\ExchangeRate as ExchangeRateModel;
use App\Models\MySql\ReportUnit as ReportUnitModel;


class CurrencyRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * 例子：
     *
     * 更新eCPM
     * php artisan auto:currency-revenue ecpm 1 USD2CNY
     *
     * 更新报表
     * php artisan auto:currency-revenue 20190101 1 USD2CNY
     *
     * @var string
     */
    protected $signature = 'auto:currency-revenue {date=-3days} {publisherId=0} {mode=USD2CNY}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动将本币为非USD的开发者收益数据转换为本币收益';

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
        $date = $this->argument('date');
        $mode = $this->argument('mode');
        if(empty($mode)){
            $mode = 'USD2CNY';
        }
        $publisherId = (int)$this->argument('publisherId');
        if(!$publisherId){
            $this->error('Publisher ID error!');
            return false;
        }
        if($date == 'ecpm'){
            $this->ecpm($publisherId, $mode);
            return false;
        }

        $currencySymbol = 'CNY';
        // 处理完历史数据后，每次只处理3天内的数据
        if($date > 0 && strlen($date) == 8){
            $startDate = $date;
        }else{
            $startDate = date('Ymd', strtotime('-3 day'));
        }

        $reportUnitModel = new ReportUnitModel();
        $reportUnit = DB::connection('bi-greenplum')
            ->table('report_unit')
            ->select('date_time') // , 'revenue', 'unit_id', 'geo_short'
            ->where('date_time', '>=', $startDate)
            ->where('publisher_id', $publisherId)
            ->where('revenue', '>', 0)
            ->groupBy('date_time')
            ->orderBy('date_time', 'asc')
            ->limit(500)
            ->get();

        $this->info(json_encode($reportUnit));

        $rows = 0;
        foreach($reportUnit as $val){
            $rows++;
            if($mode == 'CNY2USD'){
                $sqlCurrencyRevenue = DB::raw("revenue");
            }else{
                $rate = ExchangeRateModel::where('date_time', $val['date_time'])
                    ->where('currency', 'USD')
                    ->where('foreign_currency', $currencySymbol)
                    ->value('exchange_rate');
                if($rate <= 0){
                    continue;
                }
                $sqlCurrencyRevenue = DB::raw("revenue * {$rate}");
            }

            // 更新RDS
            $reportUnitModel->queryBuilder()
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);

            // 更新GreenPlum
            DB::connection('bi-greenplum')
                ->table('report_unit')
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);

            DB::connection('bi-greenplum')
                ->table('report_unit_hour')
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);

            DB::connection('bi-greenplum')
                ->table('report_unit_utc0')
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);

            DB::connection('bi-greenplum')
                ->table('report_unit_utce8')
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);

            DB::connection('bi-greenplum')
                ->table('report_unit_utcw8')
                ->where('date_time', $val['date_time'])
                ->where('publisher_id', $publisherId)
                ->update(['currency_revenue' => $sqlCurrencyRevenue]);
        }
        $this->info("Update Report Currency Revenue ({$rows} rows) Done.");
    }

    public function ecpm($publisherId, $mode)
    {
        $sqlUnit = "update unit set ecpm_currency = (ecpm * 7) where publisher_id = :publisher_id and `status` > :status;";
        $sqlUstp = "update unit_segment_placement_traffic_group set ecpm_currency = (ecpm * 7), exchange_rate = (1 / 7) where publisher_id = :publisher_id;";
        if($mode == 'CNY2USD'){
            $sqlUnit = "update unit set ecpm_currency = (ecpm / 7) where publisher_id = :publisher_id and `status` > :status;";
            $sqlUstp = "update unit_segment_placement_traffic_group set ecpm_currency = (ecpm / 7), exchange_rate = 1 where publisher_id = :publisher_id;";
        }

        // unit
        DB::connection('mysql')
            ->update($sqlUnit, [
                'publisher_id' => $publisherId,
                'status' => 0
            ]);

        // unit_segment_placement_traffic_group
        DB::connection('mysql')
            ->update($sqlUstp, [
                'publisher_id' => $publisherId
            ]);

        $this->info('Update eCPM Done.');
    }
}
