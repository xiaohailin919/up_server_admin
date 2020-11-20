<?php
/**
 * Created by PhpStorm.
 * User: 86135
 * Date: 2019/6/19
 * Time: 16:50
 */

namespace App\Services;


use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class ExchangeRateProxy
{
    /**
     * @param $fromCurrency
     * @param $toCurrency
     * @param $dateTime
     * @return bool|int|mixed
     */
    public static function getExchangeRate($fromCurrency, $toCurrency, $dateTime)
    {
        $dateTimeParam = [];
        if (is_array($dateTime)) {
            foreach ($dateTime as $d) {
                $dateTimeParam[] = $d;
            }
        } else {
            $dateTimeParam[] = $dateTime;
        }

        $retVal = [];
        foreach ($dateTimeParam as $d) {
            $retVal[$d] = 0;
        }

        $api = env('EXCHANGE_RATE');
        $urlParam = [
            "date_time" => $dateTimeParam,
            "from_currency" => $fromCurrency,
            "to_currency" => $toCurrency
        ];
        $options = [
            RequestOptions::JSON => $urlParam,
            RequestOptions::TIMEOUT => 60
        ];
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($api, $options);
            $code = $response->getStatusCode();
            if ($code != 200) {
                return false;
            }
            $body = $response->getBody();
            $result = json_decode($body, true);
            foreach ($result as $k => $v) {
                $retVal[$v['date_time']] = $v['rate'];
            }
        } catch (\Exception $e) {
            Log::debug("get exchange rate err:" . $e->getMessage());
        }

        return $retVal;
    }

    /**
     * @param $fromCurrency
     * @param $toCurrency
     * @return mixed
     */
    public static function getTodayExchangeRate($fromCurrency, $toCurrency)
    {
        $todayDate = date('Ymd');
        $currencyMap = self::getExchangeRate($fromCurrency, $toCurrency, intval($todayDate));
        return $currencyMap[0]["rate"];
    }

}