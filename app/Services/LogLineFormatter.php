<?php

namespace App\Services;
use Monolog\Formatter\JsonFormatter;

class LogLineFormatter extends JsonFormatter
{
    public function format(array $record)
    {
        $line = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'level' => str_replace(['EMERGENCY', 'ALERT', 'CRITICAL', 'WARNING'], ['ERROR', 'ERROR', 'ERROR', 'WARN'], $record['level_name']),
//            'class' => '-',
//            'file_name' => request()->server('PHP_SELF'),
            'track_id' => LOG_TRACK_ID,
            'host' => request()->server('SERVER_ADDR'),
            'url' => request()->server('REQUEST_URI'),
            'data' => '',
        ];

        $data = [];

        if (!empty($record['message'])) {
            $data['message'] = $record['message'];
        }

        if (!empty($record['extra'])) {
            $data['extra'] = $record['extra'];
        }

        if (!empty($record['context'])) {
            $data['context'] = $record['context'];
        }

        $line['data'] = $this->toJson($this->normalize($data), true);

//        return implode("\t", $line) . ($this->appendNewline ? "\n" : '');
        return $this->toJson($this->normalize($line), true) . ($this->appendNewline ? "\n" : '');
    }
}