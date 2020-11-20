<?php

namespace App\Helpers;

class Format
{
    public static function rate($install, $click, $type = 1, $overflow = false) {
        if ($install == '-' || $click == '-') return '-';

        $install = strtr($install, array(',' => ''));
        $click = strtr($click, array(',' => ''));
        $result = $click ? round($install / $click * 100, 2) : 0;
        if ($overflow && $result > 100) $result = $overflow;
        if ($result >= 50 && $type == 1) {
            $result = '<span class="red">' . $result . '%</span>';
        } else if ($result) {
            $result .= '%';
        } else {
            $result = '-';
        }
        return $result;
    }

    /**
     * 做除法运算，保留n位小数点，末位四舍五入
     * @param float $n
     * @param float $m
     * @param int $number
     * @return float
     */
    public static function division($n, $m, $number = 2){
        if($m == 0){
            return 0;
        }
        return round($n / $m, $number);
    }

    public static function ecpm($cost, $impression, $rate = 1000, $num = 3) {
        if ($cost == '-' || $impression == '-') return '-';

        $cost = strtr($cost, array(',' => ''));
        $impression = strtr($impression, array(',' => ''));
        if ($impression && $cost) $result = round($cost / $impression * $rate, $num);
        if (!isset($result) || !$result) $result = '-';

        return self::money($result, $num);
    }

    public static function money($money, $points=2) {
        if (!$money || $money == '-') {
            $result = '-';
        } else {
            $result = '<span style="color:#e4393c;">';
            if ($money < 0) $result .= '- $' . number_format(-1 * $money, 2);
            else $result .= '$' . number_format($money, $points);
            $result .= '</span>';

            if (number_format($money, $points) == '0.00') $result = '-';
        }
        return $result;
    }

    /**
     * 将时间转化为毫秒时间戳
     *
     * @param $time
     * @return float|int
     */
    public static function millisecondTimestamp($time) {
        return is_int($time) ? $time * 1000 : strtotime($time) * 1000;
    }
    
    /**
     * 格式化比率 小数转换成百分比
     *
     * @param $rate
     * @return string
     */
    public static function rateToPer($rate) {
        return floatval($rate) * 100 . "%";
    }
}
