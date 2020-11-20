<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MySql\NetworkFirm as NetworkFirmMyModel;
use App\Models\MySql\StrategyAppFirm as StrategyAppFirmMyModel;
use Illuminate\Support\Facades\Event;
use App\Events\UpdateApp;

class StrategyAppFirmController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('strategy_app');

        $appId = $request->input('app_id', 0);
        if($appId <= 0){
            exit('error.');
        }

        $strategyAppFirmMyModel = new StrategyAppFirmMyModel();
        $query = $this->indexQueryBuilder($request);
        $data = $query->orderBy('id', 'desc')->paginate(50);

        $networkFirm = new NetworkFirmMyModel();
        foreach($data as $key => $val){
            $dataTmp = $val;
            if($val['nw_firm_id'] > 0){
                $dataTmp['firm_name'] = $networkFirm->getNwFirmName($val['nw_firm_id']);
            }else{
                $dataTmp['firm_name'] = 'Default';
            }
            $dataTmp['status_name'] = $strategyAppFirmMyModel->getStatusName($val['status']);
            $data->put($key, $dataTmp);
        }
        
        $firm = $networkFirm->get(['id', 'name']);

        return view('strategy-app-firm.index')
                ->with('data', $data)
                ->with('firm', $firm)
                ->with('appId', $appId);
    }
    
    public function store(Request $request)
    {
        $this->checkAccessPermission('strategy_app_edit');

        $appId = $request->input('app_id', 0);
        $firmId = $request->input('nw_firm_id', 0);
        if($appId <= 0 || $firmId <= 0){
            exit('error.');
        }

        $strategyAppFirmMyModel = new StrategyAppFirmMyModel();
        $query = $strategyAppFirmMyModel->queryBuilder();
        
        $check = $query->where('app_id', $appId)->where('nw_firm_id', $firmId)->count();
        if($check > 0){
            exit('error.');
        }
        
        $uploadSw = $request->input('upload_sw', 0);
        $clickOnly = $request->input('click_only', 0);
        
        $data = [
            'app_id' => $appId,
            'nw_firm_id' => $firmId,
            'upload_sw' => $uploadSw,
            'click_only' => $clickOnly,
            'create_time' => time(),
            'update_time' => time(),
            'status' => $strategyAppFirmMyModel::STATUS_RUNNING,
        ];
        
        $query->insert($data);

        if ($data) {
            //触发更新placement的事件
            Event::fire(new UpdateApp($appId));
        }
        
        return redirect()->back();
    }
    
    public function edit($id)
    {
        $this->checkAccessPermission('strategy_app_edit');

        $strategyAppFirmMyModel = new StrategyAppFirmMyModel();
        $data = $strategyAppFirmMyModel->getOne([], ['id' => $id]);

        if(empty($data)){
            return 'No data.';
        }

        return view('strategy-app-firm.edit')
                ->with('data', $data);
    }
    
    public function update(Request $request, $id)
    {
        $this->checkAccessPermission('strategy_app_edit');

        $status = $request->input('status', 1);
        $input = $request->input();

        $strategyAppFirmMyModel = new StrategyAppFirmMyModel();
        $query = $strategyAppFirmMyModel->queryBuilder();

        $strategy = $strategyAppFirmMyModel->queryBuilder()->where('id', $id)->first();
        $appId = $strategy['app_id'];
        $firmId = $strategy['nw_firm_id'];

        if($firmId <= 0){
            $status = $strategyAppFirmMyModel::STATUS_RUNNING;
        }

        $data = [
            'status' => $status,
            'update_time' => time(),
        ];
        if(isset($input['upload_sw'])){
            $data['upload_sw'] = $input['upload_sw'];
        }
        if(isset($input['click_only'])){
            $data['click_only'] = $input['click_only'];
        }
        
        $query->where('id', $id)->update($data);



        if ($data) {
            //触发更新placement的事件
            Event::fire(new UpdateApp($appId));
        }

        if($request->ajax()){
            return ["status" => 1];
        }
        return redirect('strategy-app-firm?app_id=' . $appId);
    }
    
    private function indexQueryBuilder(Request $request)
    {
        $strategyAppFirmMyModel = new StrategyAppFirmMyModel();
        $query = $strategyAppFirmMyModel->queryBuilder();
        
        $appId = $request->input('app_id', 0);
        $query->where('app_id', $appId);
        $query->where('status', '>', 0);
        
        return $query;
    }
}
