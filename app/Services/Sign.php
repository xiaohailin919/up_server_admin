<?php

namespace App\Services;

class Sign
{
    /**
     * 生成签名
     * @param array $param
     * @param string $token
     * @return string
     */
    public static function generate($param, $token)
    {
        ksort($param);
        $sign = md5($token . self::httpBuildQueryNotEncode($param));
        return $sign;
    }
    
    /**
     * 生成query串，不做urlencode
     * @param array $param
     * @return string
     */
    public static function httpBuildQueryNotEncode($param)
    {
        $tmp = [];
        foreach($param as $key => $val){
            $tmp[] = $key . '=' . $val;
        }
        return implode('&', $tmp);
    }
}