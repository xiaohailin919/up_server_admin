<?php

namespace App\Services;

use App\Services\Sign;

class ReportApi
{
    private $key = '6W6PvLiKDPYe0tOk';
    private $name = 'admin';

    public function get($query)
    {
        $p = [
            'query' => base64_encode(json_encode($query)),
            'name' => $this->name,
        ];
        $p['sign'] = Sign::generate($p, $this->key);

        $url = self::getApiUrl();
        $res = self::httpPost($url, $p);

        if(!$res['status']){
            return [
                'total' => 0,
                'sql' => [],
                'list' => []
            ];
        }

        $data = $res['data'];
        if (isset($_GET['debug_trace']) && $_GET['debug_trace'] = 'debug') {
            var_dump(['query' => $query, 'data' => $data]);die;
        }

        $res = json_decode($data, true);

        return $res['data'];
    }

    private static function httpPost($url, $getData)
    {
        $ch = curl_init();

        $apiHost = self::getApiHost();
        if ($apiHost) {
            $httpHeader = array('Host: '.$apiHost);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        }

        if ($getData) {
            $url .= '?';
            foreach ($getData as $k => $v) {
                $url .= $k.'='.rawurlencode($v).'&';
            }
            $url = rtrim($url, '&');
        }
        if (isset($_GET['debug_trace']) && $_GET['debug_trace'] = 'debug') {
            echo $url;
            var_dump($url, $apiHost);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            return [
                'status' => false,
                'data'  => "",
                'msg' => 'Curl error: ' . curl_error($ch)
            ];
        }
        
        curl_close($ch);

        //打印获得的数据
        return [
            'status' => true,
            'data' => $output,
            'msg'  => ""
        ];
    }

    private static function getEnv()
    {
        return env('APP_ENV') == 'test' ? 'test' : 'product';
    }

    private static function getApiUrl()
    {
        $env = self::getEnv();
//        return 'http://local.app.uparpu.com/report_api/proxy';
        if ($env == 'test') {
            return 'http://127.0.0.1:8080/report_api/proxy';

        } else {

            return env('REPORT_API').'report_api/proxy';
        }
    }

    private static function getApiHost()
    {
        $env = self::getEnv();
        if ($env != 'test') {
            return '';
        }

        list($s, $d) = explode('http://', env('REPORT_API'));
        return rtrim($d, '/');
    }
}