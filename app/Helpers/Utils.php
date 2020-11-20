<?php
/**
 * 小工具
 */

namespace App\Helpers;

class Utils
{
    /**
     * 用正则提取 app store url 中id后面的值
     *
     * @param string $storeUrl
     * @return string
     */
    public static function getAppStoreId(string $storeUrl)
    {
        if (empty($storeUrl)) {
            return '';
        }
        preg_match('/id(\d+)/', $storeUrl, $matches);
        if (empty($matches) || !isset($matches[1])) {
            return '';
        }
        return (string)$matches[1];
    }

    /**
     * 校验string是否是JSON
     *
     * @param  string $string
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * 获取时区列表
     * @return array
     */
    public static function getTimezoneList()
    {
        $timezoneList = [
            '-12' => 'UTC-12',
            '-11' => 'UTC-11',
            '-10' => 'UTC-10',
            '-9'  => 'UTC-9',
            '-8'  => 'UTC-8',
            '-7'  => 'UTC-7',
            '-6'  => 'UTC-6',
            '-5'  => 'UTC-5',
            '-4'  => 'UTC-4',
            '-3'  => 'UTC-3',
            '-2'  => 'UTC-2',
            '-1'  => 'UTC-1',
            '0'   => 'UTC+0',
            '1'   => 'UTC+1',
            '2'   => 'UTC+2',
            '3'   => 'UTC+3',
            '4'   => 'UTC+4',
            '5'   => 'UTC+5',
            '6'   => 'UTC+6',
            '7'   => 'UTC+7',
            '8'   => 'UTC+8',
            '9'   => 'UTC+9',
            '10'  => 'UTC+10',
            '11'  => 'UTC+11',
            '12'  => 'UTC+12',
        ];
        return $timezoneList;
    }
    
    /**
     * 判断值是否是空字符串
     * 注：bool值返回true，空数组返回true
     * @param $value
     * @return bool
     */
    public static function isEmptyString($value)
    {
        return ! is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }
    
    /**
     * 获取当前格式化时间
     * @param string $format
     * @return false|string
     */
    public static function getDateTime($format = "Y-m-d H:i:s")
    {
        return date($format,time());
    }
}