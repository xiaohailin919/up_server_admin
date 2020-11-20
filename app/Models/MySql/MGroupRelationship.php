<?php

namespace App\Models\MySql;

use Illuminate\Support\Facades\DB;
use App\Models\MySqlBase;

class MGroupRelationship extends MySqlBase
{
    protected $table = 'mgroup_relationship';

    /**
     * 通过ugroup_id获取对应的unit_id
     * @param int $ugId
     * @return array
     */
    public function getUnitIdsByUgId($ugId)
    {
        $relationship = $this->get(['unit_id'], ['ugroup_id' => $ugId]);
        $unitIds = [];
        foreach($relationship as $val){
            $unitIds[] = $val['unit_id'];
        }
        return $unitIds;
    }
    
    public function getByPlacementIdOrderByEcpm($placementId, $mgroupId, $nwFirmId)
    {
        $ugroupModel = new UGroup();
        $unitModel = new Unit();
        $networkModel = new Network();
        $relationship = DB::table($this->table . ' as R')
                ->join($ugroupModel->getTable() . ' as U', 'R.ugroup_id', '=', 'U.id')
                ->join($unitModel->getTable() . ' as UN', 'UN.id', '=', 'R.unit_id')
                ->join($networkModel->getTable() . ' as N', 'N.id', '=', 'UN.network_id')
                ->select(DB::raw('R.mgroup_id as mgroup_id, U.id as ugroup_id, U.ecpm as ecpm'))
                ->where('R.placement_id', $placementId)
                ->where('R.mgroup_id', $mgroupId)
                ->where('N.nw_firm_id', $nwFirmId)
                ->orderBy('U.ecpm', 'desc')
                ->first();
        return $relationship;
    }
}