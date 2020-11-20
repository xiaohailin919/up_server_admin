<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Services\ExchangeRate as ExchangeRateService;
use App\Models\MySql\ExchangeRate as ExchangeRateModel;
use App\Models\MySql\ExchangeRateBi;

class ExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:exchange-rate {mode=latest} {currency=CNY} {date=20190505}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get latest/history exchange rate';

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
        $currency = $this->argument('currency');
        $date = $this->argument('date');

        if($mode == 'latest'){
            $this->latest($currency);
        }else if($mode == 'since'){
            $time = strtotime($date);
            // 安全考虑，一次批量获取只能获取31天数据
            for($i = 0; $i <= 30; $i++){
                $dateTmp = date('Ymd', strtotime("+{$i} day", $time));
                $this->history($dateTmp, $currency);
            }
        }else{
            $this->history($date, $currency);
        }
    }

    private function latest($currency = 'CNY'){
        $exchangeRateService = new ExchangeRateService();
        $exchangeRateModel = new ExchangeRateModel();
        $date = date('Ymd');

        // 美元兑人民币
        $rate = $exchangeRateService->latest('USD', $currency);
        if($rate <= 0){
            return false;
        }
        $data = [
            'date_time'        => $date,
            'currency'         => 'USD',
            'foreign_currency' => $currency,
            'exchange_rate'    => $rate,
            'create_time'      => date('Y-m-d H:i:s'),
        ];
        // 写uparpu_main库
        $count = $exchangeRateModel->where('date_time', $date)
            ->where('currency', 'USD')
            ->where('foreign_currency', $currency)->count();
        if($count <= 0){
            $exchangeRateModel->insert($data);
        }
        // 写bi库
        $count = ExchangeRateBi::where('date_time', $date)
            ->where('currency', 'USD')
            ->where('foreign_currency', $currency)->count();
        if($count <= 0){
            ExchangeRateBi::insert($data);
        }
        // 写bi gp库
        $count = DB::connection('bi-greenplum')
            ->table('exchange_rate')
            ->where('date_time', $date)
            ->where('currency', 'USD')
            ->where('foreign_currency', $currency)->count();
        if($count <= 0){
            unset($data['create_time']);
            DB::connection('bi-greenplum')
                ->table('exchange_rate')
                ->insert($data);
        }
        // 写bi gp2库
        $count = DB::connection('bi-greenplum-2')
            ->table('exchange_rate')
            ->where('date_time', $date)
            ->where('currency', 'USD')
            ->where('foreign_currency', $currency)->count();
        if($count <= 0){
            unset($data['create_time']);
            DB::connection('bi-greenplum-2')
                ->table('exchange_rate')
                ->insert($data);
        }

        // 汇率反转 人民币兑美元
        $rateConversion = $this->conversion($rate);
        $data = [
            'date_time'        => $date,
            'currency'         => $currency,
            'foreign_currency' => 'USD',
            'exchange_rate'    => $rateConversion,
            'create_time'      => date('Y-m-d H:i:s'),
        ];
        // 写uparpu_main库
        $count = $exchangeRateModel->where('date_time', $date)
            ->where('currency', $currency)
            ->where('foreign_currency', 'USD')->count();
        if($count <= 0){
            $exchangeRateModel->insert($data);
        }
        // 写bi库
        $count = ExchangeRateBi::where('date_time', $date)
            ->where('currency', $currency)
            ->where('foreign_currency', 'USD')->count();
        if($count <= 0){
            ExchangeRateBi::insert($data);
        }
        // 写bi gp库
        $count = DB::connection('bi-greenplum')
            ->table('exchange_rate')
            ->where('date_time', $date)
            ->where('currency', $currency)
            ->where('foreign_currency', 'USD')->count();
        if($count <= 0){
            unset($data['create_time']);
            DB::connection('bi-greenplum')
                ->table('exchange_rate')
                ->insert($data);
        }
        // 写bi gp2库
        $count = DB::connection('bi-greenplum-2')
            ->table('exchange_rate')
            ->where('date_time', $date)
            ->where('currency', $currency)
            ->where('foreign_currency', 'USD')->count();
        if($count <= 0){
            unset($data['create_time']);
            DB::connection('bi-greenplum-2')
                ->table('exchange_rate')
                ->insert($data);
        }

        return;
    }

    private function history($date, $currency = 'CNY'){
        if(empty($date) || !is_numeric($date)){
            return false;
        }
        $exchangeRateService = new ExchangeRateService();
        $exchangeRateModel = new ExchangeRateModel();

        // 美元兑人民币
        $rate = $exchangeRateService->history($date, $currency);
        if($rate <= 0){
            return false;
        }
        $data = [
            'date_time' => $date,
            'currency' => 'USD',
            'foreign_currency' => $currency,
            'exchange_rate' => $rate
        ];
        $count = $exchangeRateModel->where('date_time', $date)
            ->where('currency', 'USD')
            ->where('foreign_currency', $currency)->count();
        if($count <= 0){
            $exchangeRateModel->insert($data);
        }

        // 汇率反转 人民币兑美元
        $rateConversion = $this->conversion($rate);
        $data = [
            'date_time' => $date,
            'currency' => $currency,
            'foreign_currency' => 'USD',
            'exchange_rate' => $rateConversion
        ];
        $count = $exchangeRateModel->where('date_time', $date)
            ->where('currency', $currency)
            ->where('foreign_currency', 'USD')->count();
        if($count <= 0){
            $exchangeRateModel->insert($data);
        }

        return true;
    }

    private function conversion($rate){
        if($rate <= 0){
            return 0;
        }
        return 1 / $rate;
    }
}
