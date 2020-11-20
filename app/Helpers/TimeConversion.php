<?php

namespace App\Helpers;

class TimeConversion
{
    public static function hourToMinute($hour)
    {
        self::check($hour);
        return 60 * $hour;
    }

    public static function minuteToSecond($minute)
    {
        self::check($minute);
        return 60 * $minute;
    }

    public static function secondToMinute($second)
    {
        self::check($second);
        return $second / 60;
    }
    
    public static function secondToMs($second)
    {
        self::check($second);
        return 1000 * $second;
    }

    public static function hourToMs($hour)
    {
        $minute = self::hourToMinute($hour);
        $second = self::minuteToSecond($minute);
        return self::secondToMs($second);
    }

    public static function minuteToMs($minute)
    {
        $second = self::minuteToSecond($minute);
        return self::secondToMs($second);
    }
    
    /**
     * 将 A时区时间戳 转换为 B时区时间戳
     * @param int/string $newTimezone
     * @param int $timezone
     * @param int $time
     * @return int
     */
    public static function convertTimezone($newTimezone, $timezone = 8, $time = 0)
    {
        if(!$time){
            $time = time();
        }
        if($timezone < -12 || $timezone > 12){
            $timezone = 8;
        }
        $tz = str_replace(['GMT', ':00'], '', $newTimezone);
        if($tz < -12 || $tz > 12){
            $tz = 8;
        }
        $hour = intval($tz) - $timezone;
        return strtotime(gmdate('Y-m-d H:i:s', $time) . " {$hour} hours");
    }

    /**
     * 将时间转化为毫秒时间戳
     *
     * @param $time
     * @return float|int
     */
    public static function dateTimeToMsTimestamp($time) {
        return is_int($time) ? $time * 1000 : strtotime($time) * 1000;
    }

    private static function check($num)
    {
        if(!is_numeric($num)){
            throw new Exception("must be numeric");
        }
    }
}