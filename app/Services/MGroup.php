<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\MySql\Unit as UnitMyModel;
use App\Models\MySql\Network as NetworkMyModel;
use App\Models\MySql\NetworkFirm as NetworkFirmMyModel;
use App\Models\MySql\MGroup as MGroupMyModel;
use App\Models\MySql\MGroupRule as MGroupRuleMyModel;
use App\Models\MySql\UGroup as UGgroupMyModel;
use App\Models\MySql\StrategyPlacementMGroup as StrategyPlacementMGroupMyModel;
use App\Models\MySql\FirmAdapter as FirmAdapterMyModel;

class MGroup
{
    private $tableMGroup = 'mgroup';
    private $tableMGroupRelationship = 'mgroup_relationship';
    private $tableMGroupRule = 'mgroup_rule';
    private $tableUnit = 'unit';
    private $tableNetwork = 'network';
    private $tableUGroup = 'ugroup';
    
    public function getRelationshipByPid($placementId)
    {
        if($placementId <= 0){
            return [];
        }
        $list = DB::table("{$this->tableMGroupRelationship} as mgr")
                    ->select('mgr.placement_id', 'mgr.mgroup_id', 'mg.rank', 'mg.update_time')
                    ->leftJoin("{$this->tableMGroup} as mg", 'mg.id', '=', 'mgr.mgroup_id')
                    ->where('mgr.placement_id', $placementId)
                    ->where('mg.status', MGroupMyModel::STATUS_RUNNING)
                    ->groupBy('mgr.placement_id', 'mgr.mgroup_id', 'mg.rank', 'mg.update_time')
                    ->orderBy('mg.rank', 'asc')
                    ->orderBy('mg.update_time', 'desc')
                    ->get();
        return $list;
    }
    
    public function getMGroupByPid($pid, $system)
    {
        $relationship = $this->getRelationshipByPid($pid);
        $mGroup = [];
        $mGroupRuleMyModel = new MGroupRuleMyModel();
        $strPlMGroupModel = new StrategyPlacementMGroupMyModel();
        foreach($relationship as $val){
            $mGroupItem = [];
            $mGroupItem['id'] = $val['mgroup_id'];
            $mGroupItem['rank'] = $val['rank'];
            $rule = $mGroupRuleMyModel->getRule($val['mgroup_id']);
            $mGroupItem['condition'] = $this->formatRule($rule);
            $mGroupItem['unit'] = $this->getUnits($val, $system);
            $mGroupItem['strategy'] = (array)$strPlMGroupModel->getOne(['pacing', 'cap_hour', 'cap_day'], ['placement_id' => $pid, 'mgroup_id' => $val['mgroup_id']]);
            $mGroup[] = $mGroupItem;
        }
        
        return $mGroup;
    }
    
    /**
     * 格式化rule
     * @param object $rule
     * @return object
     */
    private function formatRule($rule)
    {
        if(empty($rule)){
            return [];
        }
        $return = [];
        foreach($rule as $val){
            $item = [];
            $item['type'] = $val['type'];
            $item['rule'] = $val['rule'];
            if(in_array($val['rule'], [MGroupRuleMyModel::RULE_GTE, MGroupRuleMyModel::RULE_LTE])){
                $item['rule_content'] = $val['rule_content'];
            }else if($val['rule'] == MGroupRuleMyModel::RULE_CUSTOM){
                $item['rule_content'] = $this->formatRuleCustom($val['rule_content']);
            }else{
                $item['rule_content'] = json_decode($val['rule_content'], TRUE);
            }
            $return[] = $item;
        }
        return $return;
    }
    
    /**
     * 格式化custom rule
     * @param string $content
     * @return object
     */
    private function formatRuleCustom($content)
    {
        $rule = [];
        $rule1 = explode("\n", $content);
        foreach($rule1 as $val1){
            $rule[] = $this->formatRuleCustomSub($val1);
        }
        return $rule;
    }
    
    /**
     * 格式化custom rule子集
     * @param string $content
     * @return array
     */
    private function formatRuleCustomSub($content)
    {
        $rule = [];
        $rule1 = explode("&", $content);
        foreach($rule1 as $val1){
            if(stripos($val1, '!=')){
                $val2 = explode('!=', $val1);
                $rule2 = [$val2[0], '!=', $val2[1]];
            }else{
                $val2 = explode('=', $val1);
                $rule2 = [$val2[0], '=', $val2[1]];
            }
            $rule[] = $rule2;
        }
        return $rule;
    }
    
    private function getUnits($ids, $system)
    {
        $units = DB::table("{$this->tableMGroupRelationship} as r")
                    ->select('r.unit_id', 'r.placement_id', 'r.mgroup_id', 'r.ugroup_id')
                    ->leftJoin("{$this->tableUGroup} as ug", 'ug.id', '=', 'r.ugroup_id')
                    ->where('r.placement_id', $ids['placement_id'])
                    ->where('r.mgroup_id', $ids['mgroup_id'])
                    ->orderBy('ug.ecpm', 'desc')
                    ->get();
        $return = [];
        foreach($units as $val){
            $unit = $this->getUnit($val, $system);
            if(empty($unit)){
                continue;
            }
            $return[] = $unit;
        }
        return $return;
    }
    
    private function getUnit($ids, $system)
    {
        $unitMyModel = new UnitMyModel();
        $uGroupMyModel = new UGgroupMyModel();
        $networkMyModel = new NetworkMyModel();
//        $networkFirmMyModel = new NetworkFirmMyModel();
        
        $unitField = [
            'id as unit_id',
            'network_id', 
            'remote_unit',
        ];
        
        $unit = (array)$unitMyModel->getOne($unitField, ['id' => $ids['unit_id'], 'status' => UnitMyModel::STATUS_RUNNING]);
        if(empty($unit)){
            return [];
        }
        $return = $unit;
        $return['ugroup_id'] = $ids['ugroup_id'];
        $return['network'] = (array)$uGroupMyModel->getOne(['ecpm', 'cap_hour', 'cap_day'], ['id' => $ids['ugroup_id']]);
        $networkFirmId = $networkMyModel->getFirmId($unit['network_id']);
        $return['network']['firm_id'] = $networkFirmId;
//        $firmConfig = $networkFirmMyModel->getFirmConfig($networkFirmId);
//        $return['network']['sdk_parameter'] = $firmConfig['sdk_parameter'];

        $firmAdapterMyModel = new FirmAdapterMyModel();
//        旧写法，已不适用
//        $fWhere = [
//            'firm_id' => $networkFirmId,
//            'system' => $system,
//        ];
//        $adapterLists = $firmAdapterMyModel->get(['adapter', 'platform', 'format'], $fWhere);
        $adapterLists = $firmAdapterMyModel->newQuery()
            ->where('firm_id', '=',$networkFirmId)
            ->where('system', '=', $system)
            ->get(['adapter', 'platform', 'format']);
        foreach($adapterLists as $k => $v) {
            if($v['platform'] == 1 && $v['format'] == 0) {
                $return['network']['adapter_class'] = $v['adapter'];
            } elseif ($v['platform'] == 2 && $v['format'] == 0) {
                $return['network']['ios_adapter_class'] = $v['adapter'];
            } elseif ($v['platform'] == 1 && $v['format'] == 1) {
                $return['network']['adapter_class_rv'] = $v['adapter'];
            } elseif ($v['platform'] == 2 && $v['format'] == 1) {
                $return['network']['ios_adapter_class_rv'] = $v['adapter'];
            } elseif ($v['platform'] == 1 && $v['format'] == 2) {
                $return['network']['adapter_class_banner'] = $v['adapter'];
            } elseif ($v['platform'] == 2 && $v['format'] == 2) {
                $return['network']['ios_adapter_class_banner'] = $v['adapter'];
            } elseif ($v['platform'] == 1 && $v['format'] == 3) {
                $return['network']['adapter_class_interstitial'] = $v['adapter'];
            } elseif ($v['platform'] == 2 && $v['format'] == 3) {
                $return['network']['ios_adapter_class_interstitial'] = $v['adapter'];
            } elseif ($v['platform'] == 1 && $v['format'] == 4) {
                $return['network']['adapter_class_splash'] = $v['adapter'];
            } elseif ($v['platform'] == 2 && $v['format'] == 4) {
                $return['network']['ios_adapter_class_splash'] = $v['adapter'];
            } else {
                return [];
            }
        }

        return $return;
    }
}

