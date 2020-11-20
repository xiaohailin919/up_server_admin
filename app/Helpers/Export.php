<?php
/**
 * 导出数据helper
 */

namespace App\Helpers;

class Export
{
    private static function excelHeader($fields, $filename, $unset = [])
    {
        header('Content-type:application/octet-stream');
        header('Accept-Ranges:bytes');
        header('Content-type:application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename=' . (strtr($filename, array(' ' => '_')) . '_' . time()) . '.xls');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');

        // 导出xls 开始
        foreach ($fields as $field => $val) {
            if (in_array($field, $unset)) {
                continue;
            }
            echo iconv('UTF-8', 'GB2312//ignore', strtr(strip_tags($val), array('<br/>' => ' '))) . "\t";
        }
        echo "\n";
    }

    public static function excel($fields, $list, $header = true, $filename = 'export', $unset = [], $convert = [])
    {
        set_time_limit(0);
        if ($header) {
            self::excelHeader($fields, $filename, $unset);
        }

        $t = "\t";
        foreach ($list as $key => $val) {
            $content = '';
            foreach ($fields as $field => $v) {
                if (in_array($field, $unset)) {
                    continue;
                }
                if (isset($val[$field])) {
                    $val[$field] = strip_tags($val[$field]);
                    if (is_numeric($val[$field]) || !$val[$field]) {
                        $content .= $val[$field] . $t;
                    } else {
                        if (in_array($field, $convert)) {
                            if (strpos($val[$field], '&#039') !== false) {
                                $content .= '"' . mb_convert_encoding($val[$field], 'UTF-8', 'HTML-ENTITIES') . '"' . $t;
                            } else {
                                $content .= '"' . $val[$field] . '"' . $t;
                            }
                        } else {
                            $content .= mb_convert_encoding($val[$field], 'GBK', 'UTF-8') . $t;
                        }
                    }
                } else {
                    $content .= '0' . $t;
                }
            }
            echo $content .= "\n";
        }
        exit;
    }

    /**
     * 以 CSV 的格式导出
     *
     * @param array $data
     * @param array $headMap
     * @param $fileName
     */
    public static function exportAsCsv($data, $headMap, $fileName) {
        self::setCsvHeader($fileName);

        // 打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'ab');

        /* 写入 UTF-8 BOM 头部 */
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        /* 写入表格头 */
        fputcsv($fp, array_values($headMap));

        // 逐行取出数据，不浪费内存
        for ($i = 0, $iMax = count($data), $row = []; $i < $iMax; $i++, $row = []) {

            foreach ($headMap as $metric => $header) {
                $row[] = $data[$i][$metric] ?? '';
            }
            fputcsv($fp, $row);

            /* 设置一个 100000 条记录的缓冲区，每到 100000 条记录就刷一遍缓冲区 */
            if ($i % 100000 == 0) {
                ob_flush();
                flush();
            }
        }
    }

    /**
     * 设置 CSV 模式响应头
     *
     * @param $fileName
     */
    private static function setCsvHeader($fileName) {
        header("Content-Type:text/csv;charset=utf-8");
        header("Content-Disposition:attachment;filename=" . $fileName);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0,max-age=0');
        header('Expires:0');
        header('Pragma:public');
    }

    /**
     * 读取 CSV 文件中的内容
     *
     * @param $filename
     * @param $hasHeader
     * @return array
     */
    public static function readCsv($filename, $hasHeader): array
    {
        $file = fopen($filename, "rb");

        $headerList = [];
        if ($hasHeader) {
            $headerList = fgetcsv($file);
        }

        $data = [];
        while (!feof($file)) {
            $data[] = fgetcsv($file);
        }
        fclose($file);

        return ['header' => $headerList, 'data' => $data];
    }

}
