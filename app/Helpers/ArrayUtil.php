<?php

namespace App\Helpers;

class ArrayUtil
{
    public static function groupBy($arr, $key)
    {
        $grouped = [];
        foreach ($arr as $value) {
            $grouped[$value[$key]][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('\App\Helpers\ArrayUtil::groupBy', $parms);
            }
        }
        return $grouped;
    }

    public static  function sortArrByManyField(){
        $args = func_get_args();
        if(empty($args)){
            return null;
        }
        $arr = array_shift($args);
        if(!is_array($arr)){
            throw new Exception("第一个参数不为数组");
        }
        foreach($args as $key => $field){
            if(is_string($field)){
                $temp = array();
                foreach($arr as $index=> $val){
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr;//引用值
        call_user_func_array('array_multisort',$args);
        return array_pop($args);
    }

    /**
     * 将数组中的元素转化为整型
     *
     * @param $array
     * @return mixed
     */
    public static function intElements(&$array)
    {
        foreach ($array as $idx => $value) {
            $array[$idx] = (int)$value;
        }
        return $array;
    }


    /**
     * Description:获取中枢点的位置
     *
     * @param array $array
     * @param int $left
     * @param int $right
     * @param string $field
     * @return int
     */
    public static function fetchArrayPivot (&$array, $left, $right, $field)
    {
        // 基准定义
        $stand = $array[$left];

        // 遍历数组
        while ($left < $right) {
            while ($left < $right && $array[$right][$field] >= $stand[$field]) {
                $right --;
            }
            if ($left < $right) {
                $array[$left ++] = $array[$right];
            }

            while ($left < $right && $array[$left][$field] <= $stand[$field]) {
                $left ++;
            }
            if ($left < $right) {
                $array[$right --] = $array[$left];
            }
        }

        // 获取中枢点位置
        $array[$left] = $stand;

        return $left;
    }

    /**
     * Description:快速排序主程序
     *
     * @param array $array
     * @param int $begin
     * @param int $end
     * @param string $field
     */
    public static function quickSort (&$array, $begin, $end, $field)
    {
        // 变量定义
        $pivot = null;

        if ($begin < $end) {
            $pivot = ArrayUtil::fetchArrayPivot($array, $begin, $end, $field);
            ArrayUtil::quickSort($array, $begin, $pivot - 1, $field);
            ArrayUtil::quickSort($array, $pivot + 1, $end, $field);
        }
    }

    //替换adapter
    public static function replaceAdapter($str) {
        $str = str_replace("uparpu", "anythink", $str);
        $str = str_replace("UpArpu", "AT", $str);
	    $str = str_replace("UPArpu", "AT", $str);
        return $str;
    }

    /**
     * 拆分字符串为数组，并过滤空值
     * @param  string $string
     * @param  string $delimiter
     * @param  string $typeOf
     * @return array
     */
    public static function explodeString($string, $delimiter = ',', $typeOf = 'string'){
        if(empty($string)){
            return [];
        }
        $array = explode($delimiter, $string);
        $return = [];
        foreach($array as $val){
            $val = trim($val);
            if(empty($val)){
                continue;
            }
            $return[] = ($typeOf == 'int') ? (int)$val : (string)$val;
        }
        return $return;
    }

    /**
     * 对分段式数字字符串进行排序，需指定分隔符、段长度
     *
     * @param array $array
     * @param string $separator 分隔符
     * @param int $segment 段长度
     * @param bool $asc true 为升序排序，false 为降序排序
     * @return array
     */
    public static function sortStrArrWithSegments(array $array, string $separator, int $segment, bool $asc): array
    {
        $res = [];

        if (empty($array)) {
            return [];
        }

        $array = array_values($array);

        if ($separator == '' || $segment <= 0) {
            $asc ? sort($array) : rsort($array);
            return $array;
        }

        for ($i = 0, $iMax = count($array); $i < $iMax; $i++) {
            $tmp = explode($separator, $array[$i]);
            /* 按指定 segment 长度进行遍历 */
            for ($j = 0, $jMax = $segment > count($tmp) ? count($tmp) : $segment; $j < $jMax; $j++) {
                /* 长度扩充至 10 位，补空格 */
                $res[$i][] = str_pad($tmp[$j], 10, ' ', STR_PAD_LEFT);
            }
            $res[$i] = implode(".", $res[$i]);
        }

        $asc ? sort($res) : rsort($res);

        for ($i = 0, $iMax = count($res); $i < $iMax; $i++) {
            $tmp = explode(".", $res[$i]);
            $res[$i] = [];
            /* 去掉前面的 0 */
            foreach ($tmp as $item) {
                $res[$i][] = ltrim($item, ' ');
            }
            $res[$i] = implode($separator, $res[$i]);
        }

        return $res;
    }
}
