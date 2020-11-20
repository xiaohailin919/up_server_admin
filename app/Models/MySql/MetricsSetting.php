<?php

namespace App\Models\MySql;

use Illuminate\Support\Facades\Auth;

class MetricsSetting extends Base
{
    protected $table = 'metrics_setting';

    public function getFullReportSettings(){
        return $this->where('kind', 7)
            ->where('publisher_id', 0)
            ->where('admin_id', Auth::id())
            ->get()
            ->toArray();
    }

    public function getFullReportSettingFields(){
        $data = $this->from($this->table . ' as ms')
            ->leftJoin('metrics_report as mr', 'mr.id', '=', 'ms.metrics_id')
            ->select(
                'mr.field as field',
                'mr.name as name'
            )
            ->where('ms.kind', 7)
            ->where('ms.publisher_id', 0)
            ->where('admin_id', Auth::id())
            ->orderBy('mr.priority', 'desc')
            ->get()
            ->toArray();

        if(empty($data)){
            $data = MetricsReport::where('kind', 7)
                ->orderBy('priority', 'desc')
                ->get()
                ->toArray();
        }

        return array_column($data, 'name', 'field');
    }
}
