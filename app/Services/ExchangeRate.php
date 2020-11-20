<?php

namespace App\Services;


use Illuminate\Support\Facades\Log;

class ExchangeRate{
    private $nowapiAppKey = '38118';
    private $nowapiSign = '562658c2cf5414e4b5a2f5ce202c0157';

    /**
     * 最新汇率
     * @param string $currency
     * @param string $foreignCurrency
     * @return mixed
     */
    public function latest($currency = 'CNY', $foreignCurrency = 'USD'){
        $param = [
            'scur' => strtoupper($currency), // 本币
            'tcur' => strtoupper($foreignCurrency), // 外币
            'app' => 'finance.rate',
        ];
        $data = $this->nowapi($param);
        return $data['rate'];
    }

    /**
     * 历史汇率
     * 接口限制，目前只能取到美元兑其他货币的汇率
     * @param $date
     * @param string $currency
     * @return bool/float
     */
    public function history($date, $currency = 'CNY'){
        if(empty($date) || empty($currency)){
            return false;
        }
        $param = [
            'date' => $date,
            'curno' => strtoupper($currency),
            'app' => 'finance.rate_history',
        ];
        $data = $this->nowapi($param);
        return isset($data[0]) ? $data[0]['rate'] : false;
    }

    /**
     * NowAPI
     * @param $param
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function nowapi($param){
        if(!is_array($param)){
            return false;
        }
        //combinations
        $param['appkey'] = $this->nowapiAppKey;
        $param['sign'] = $this->nowapiSign;
        $param['format'] = empty($param['format']) ? 'json' : $param['format'];
        $param['app'] = $param['app'];
        $api = 'https://sapi.k780.com/?';
        foreach($param as $k => $v){
            $api .= $k . '=' . $v . '&';
        }
        $api = substr($api,0,-1);

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', $api, [
                'query' => $param,
//                'debug' => true
            ]);
            $body = $response->getBody();
            $data = json_decode($body, true);

            // array
            if($data['success'] != '1'){
                echo $data['msgid'] . ' ' . $data['msg'];
                return false;
            }

            return $data['result'];
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Connect error
            Log::error('Exchange rate service Connect error.');
            return false;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Client error
            Log::error('Exchange rate service Client error.');
            return false;
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Server error
            Log::error('Exchange rate service Server error.');
            return false;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Request error
            Log::error('Exchange rate service Request error.');
            return false;
        }
    }
}