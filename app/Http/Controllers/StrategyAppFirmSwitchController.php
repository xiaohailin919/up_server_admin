<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MySql\NetworkFirm as NetworkFirmMyModel;
use App\Models\MySql\StrategyAppFirmSwitch as StrategyAppFirmSwitchMyModel;

class StrategyAppFirmSwitchController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('strategy_app_firm_switch');

        $appId = $request->input('app_id', 0);
        if($appId <= 0){
            exit('error.');
        }

        $strategyAppFirmSwitchMyModel = new StrategyAppFirmSwitchMyModel();

        $defaultFirm = $strategyAppFirmSwitchMyModel->queryBuilder()->where("app_id", 0)->where("nw_firm_id", 0)->first();
        $defaultFirm['firm_name'] = "Default";
        $defaultFirm['status_name'] = $strategyAppFirmSwitchMyModel->getStatusName($defaultFirm['status']);

        $query = $this->indexQueryBuilder($request);
        $data = $query->orderBy('id', 'desc')->paginate(50);

        $networkFirm = new NetworkFirmMyModel();
        foreach($data as $key => $val){
            $dataTmp = $val;
            $dataTmp['firm_name'] = $networkFirm->getNwFirmName($val['nw_firm_id']);
            $dataTmp['status_name'] = $strategyAppFirmSwitchMyModel->getStatusName($val['status']);
            $data->put($key, $dataTmp);
        }
        
        $firm = $networkFirm->get(['id', 'name']);

        return view('strategy-app-firm-switch.index')
                ->with('data', $data)
                ->with('firm', $firm)
                ->with('defaultItem', $defaultFirm)
                ->with('pauseStatus', $strategyAppFirmSwitchMyModel::STATUS_PAUSED)
                ->with('activeStatus', $strategyAppFirmSwitchMyModel::STATUS_ACTIVE)
                ->with('appId', $appId);
    }
    
    public function store(Request $request)
    {
        $this->checkAccessPermission('strategy_app_firm_switch_store');

        $appId = $request->input('app_id', 0);
        $firmId = $request->input('nw_firm_id', 0);
        if($appId <= 0 || $firmId <= 0){
            exit('error appId or firmId.');
        }

        $strategyAppFirmSwitchMyModel = new StrategyAppFirmSwitchMyModel();
        $query = $strategyAppFirmSwitchMyModel->queryBuilder();

        if($query->where('app_id', $appId)->where('nw_firm_id', $firmId)->exists()){
            exit('error.');
        }

        $filledRequestSwitch = $request->input('filled_request_switch', 0);
        $impressionSwitch = $request->input('impression_switch', 0);
        $showSwitch = $request->input('show_switch', 0);
        $clickSwitch = $request->input('click_switch', 0);
        $pkg = $request->input('pkg', "");
        if($pkg != "" && !$this->isPkgJsonFormat($pkg)){
            exit("pkg must be json string2");
        }

        $data = [
            'app_id' => $appId,
            'nw_firm_id' => $firmId,
            'filled_request_switch' => $filledRequestSwitch,
            'impression_switch' => $impressionSwitch,
            'show_switch' => $showSwitch,
            'click_switch' => $clickSwitch,
            'package_match_list' => $pkg,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'status' => $strategyAppFirmSwitchMyModel::STATUS_ACTIVE,
        ];
        
        $query->insert($data);
        
        return redirect()->back();
    }
    
    public function edit($id, Request $request)
    {
        $this->checkAccessPermission('strategy_app_firm_switch_edit');

        $currentAppId = $request->input("current_app_id", 0);
        $strategyAppFirmMyModel = new StrategyAppFirmSwitchMyModel();
        $data = $strategyAppFirmMyModel->getOne([], ['id' => $id]);

        if(empty($data)){
            return 'No data.';
        }

        return view('strategy-app-firm-switch.edit')
            ->with('currentAppId', $currentAppId)
            ->with('statusMap', $strategyAppFirmMyModel->getStatusMap())
                ->with('data', $data);
    }
    
    public function update(Request $request, $id)
    {
        $this->checkAccessPermission('strategy_app_firm_switch_edit');

        $status = $request->input('status', 1);
        $input = $request->input();

        $strategyAppFirmSwitchMyModel = new StrategyAppFirmSwitchMyModel();
        $query = $strategyAppFirmSwitchMyModel->queryBuilder();

        $strategy = $strategyAppFirmSwitchMyModel->queryBuilder()->where('id', $id)->first();
        $appId = $request->input("current_app_id", 0);
        $firmId = $strategy['nw_firm_id'];

        if($firmId <= 0){
            $status = $strategyAppFirmSwitchMyModel::STATUS_ACTIVE;
        }

        $data = [
            'status' => $status,
            'update_time' => date('Y-m-d H:i:s'),
        ];
        if(isset($input['filled_request_switch'])){
            $data['filled_request_switch'] = $input['filled_request_switch'];
        }
        if(isset($input['impression_switch'])){
            $data['impression_switch'] = $input['impression_switch'];
        }
        if(isset($input['show_switch'])){
            $data['show_switch'] = $input['show_switch'];
        }
        if(isset($input['click_switch'])){
            $data['click_switch'] = $input['click_switch'];
        }

        $pkg = $request->input("pkg", "{}");
        if($pkg != "" && !$this->isPkgJsonFormat($pkg)){
            exit("pkg must be json string");
        }
        $data['package_match_list'] = $pkg;
        
        $query->where('id', $id)->update($data);

        if($request->ajax()){
            return ["status" => 1];
        }
        return redirect('strategy-app-firm-switch?app_id=' . $appId);
    }

    private function isPkgJsonFormat($pkg) { json_decode($pkg); return (json_last_error() == JSON_ERROR_NONE); }
    
    private function indexQueryBuilder(Request $request)
    {
        $strategyAppFirmSwitchMyModel = new StrategyAppFirmSwitchMyModel();
        $query = $strategyAppFirmSwitchMyModel->queryBuilder();
        
        $appId = $request->input('app_id', 0);
        $query->where('app_id', $appId);
        $query->where('status', '>', 0);
        
        return $query;
    }
}
