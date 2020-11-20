<?php

namespace App\Models\MySql;

use Illuminate\Support\Facades\DB;

class ReportUnit extends Base
{
    protected $connection = 'bi';
    protected $table = 'report_unit';

    /**
     * 兼容处理
     * @return \Illuminate\Database\Query\Builder
     */
    public function queryBuilder()
    {
        return $this;
    }
    /**
     * 通过unit id获取对应的报表，如果不满足条件则返回空数据
     * @param array $unitId
     * @return array
     */
    public function getReportByUnitId($unitId)
    {
        $sinceTime = date('Ymd', strtotime('-3 days'));
        $query = $this->where('date_time', '>=', $sinceTime)
                ->whereIn('unit_id', $unitId);
        $collection = $query->select('date_time', DB::raw('SUM(impression) as impression'), DB::raw('SUM(revenue) as revenue'))
                ->groupBy('date_time')
                ->get();
        
        $total = $collection->count();
        if($total < 3){
            return [];
        }
        
        $data = [
            'impression' => 0,
            'revenue' => 0,
        ];
        foreach($collection as $val){
            $data['impression'] += $val['impression'];
            $data['revenue'] += $val['revenue'];
        }

        if(!isset($data['impression']) || $data['impression'] < 5000){
            return [];
        }
        return $data;
    }
    
}