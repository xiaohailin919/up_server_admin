<?php
/**
 * Created by PhpStorm.
 * User: Liu
 * Date: 2019/12/26
 * Time: 1:54 PM
 */

namespace App\Services;

use App\Helpers\ArrayUtil;
use App\Models\MySql\StrategyApp as StrategyAppModel;
use App\Models\MySql\App;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\FirmAdapter;
use App\Models\MySql\Unit;

class StrategyApp
{
    public function genNetworkPreInitList($networkIds, $app){
        $return  = [];
        foreach($networkIds as $networkId){
            if(!in_array($networkId, array_keys(StrategyAppModel::$androidInitAdapter))){
                continue;
            }

            $tmp = [];

            // adapter
            if($app['platform'] == App::PLATFORM_ANDROID){
                $tmp['adapter'][] = StrategyAppModel::$androidInitAdapter[$networkId];
            }else{
                $firmAdapterModel = new FirmAdapter();
                $networkFirm = $firmAdapterModel->newQuery()
                    ->where('system', NetworkFirm::SYSTEM_UP)
                    ->where('platform', NetworkFirm::PLATFORM_IOS)
                    ->where('firm_id', $networkId)
                    ->get();
                foreach($networkFirm as $val){
                    $tmp['adapter'][] = ArrayUtil::replaceAdapter($val['adapter']);
                }
            }

            // content
            $unitModel = new Unit();
            $unit = $unitModel->queryBuilder()->from('unit as u')
                ->leftJoin('placement as p', 'p.id', '=', 'u.placement_id')
                ->leftJoin('network as n', 'n.id', '=', 'u.network_id')
                ->select('u.remote_unit as remote_unit')
                ->where('u.status', Unit::STATUS_RUNNING)
                ->where('u.publisher_id', $app['publisher_id'])
                ->where('p.app_id', $app['id'])
                ->where('n.nw_firm_id', $networkId)
                ->first();
            if(empty($unit)){
                $tmp['content'] = "";
            }else{
                $tmp['content'] = $unit['remote_unit'];
            }
            $return[$networkId] = $tmp;
        }
        return $return;
    }
}