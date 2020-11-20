<?php

namespace App\Imports;

use App\Models\MySql\ReportUnit as ReportUnitModel;
use App\Models\MySql\Unit as UnitModel;
use App\Models\MySql\Placement as PlacementModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BaiduReportImport implements ToCollection
{
    private $setting;
    private $count = 0;

    public function setSetting(array $setting)
    {
        $this->setting = $setting;
    }

    public function getCount(){
        return $this->count;
    }

    /**
    * @param array $row
    *
    */
    public function collection(Collection $rows)
    {
        $reportUnitModel = new ReportUnitModel();
        $unitModel = new UnitModel();
        $placementModel = new PlacementModel();
        foreach ($rows as $key => $row)
        {
            $importUnitId = $row[0];
            if($key <= 0 || $importUnitId <= 0){
                continue;
            }
            $dateTime = date('Ymd', strtotime($row[2]));

            // 获取Unit信息
            $unit = $unitModel->queryBuilder()
                ->where('publisher_id', $this->setting['publisher_id'])
                ->where('remote_unique', $importUnitId)
                ->first();
            if(empty($unit)){
                continue;
            }

            // 检查是否有旧数据
            $check = $reportUnitModel->queryBuilder()
                ->where('date_time', $dateTime)
                ->where('geo_short', 'CN')
                ->where('unit_id', $unit['id'])
                ->count();

            // 删除旧数据
            if($check > 0){
                $reportUnitModel->queryBuilder()
                    ->where('date_time', $dateTime)
                    ->where('geo_short', 'CN')
                    ->where('unit_id', $unit['id'])
                    ->delete();
            }

            // 写入新数据
            $placement = $placementModel->queryBuilder()
                ->where('id', $unit['placement_id'])
                ->first();
            $data = [
                'date_time' => $dateTime,
                'nw_firm_id' => 22,
                'geo_short' => 'CN',
                'publisher_id' => $this->setting['publisher_id'],
                'app_id' => $placement['app_id'],
                'placement_id' => $placement['id'],
                'unit_id' => $unit['id'],
                'format' => $placement['format'],
                'request' => 0,
                'filled_request' => 0,
                'impression' => $row[3],
                'click' => $row[4],
                'rv_start' => 0,
                'rv_complete' => 0,
                'revenue' => $row[5] * $this->setting['exchange_rate'],
                'origin_revenue' => $row[5],
                'exchange_rate' => $this->setting['publisher_id'],
                'update_time' => time(),
            ];
            $reportUnitModel->queryBuilder()->insert($data);
            $this->count++;
        }
    }
}
