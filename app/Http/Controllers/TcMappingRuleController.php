<?php

namespace App\Http\Controllers;

use App\Models\MySql\NetworkFirm as NetworkFirmMyModel;
use App\Models\MySql\TcMappingRule as TcMappingRuleMyModel;
use App\Models\MySql\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TcMappingRuleController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('tc_mapping_rules');

        $nwFirmId = $request->input('nw_firm_id', '');
        $nwFirmName = $request->input('nw_firm_name', '');
        $status = $request->input('status', 'all');

        $tcMappingRuleMyModel = new TcMappingRuleMyModel();
        $query = DB::table('tc_mapping_rule as tmr')
            ->leftJoin('network_firm as nwf', 'nwf.id', '=', 'tmr.nw_firm_id')
            ->select('tmr.*', 'nwf.name as nw_firm_name');
        if ($nwFirmId) {
            $query->where('tmr.nw_firm_id', $nwFirmId);
        }
        if ($nwFirmName) {
            $query->where('nwf.name', 'like', "%{$nwFirmName}%");
        }
        if ($status != "all" && in_array($status, array_keys($tcMappingRuleMyModel->getStatusMap()))) {
            $query->where('tmr.status', $status);
        } else {
            $query->where('tmr.status', '>', -1);
        }

        $data = $query->orderBy('tmr.id', 'desc')->paginate(10);
        $statusMap = $tcMappingRuleMyModel->getStatusMap();
        foreach ($data as $key => $val) {
            $data->put($key, $this->unserializeItem($val, $statusMap));
        }

        return view('tc-mapping-rule.index')
            ->with('data', $data)
            ->with('statusMap', $tcMappingRuleMyModel->getStatusMap())
            ->with('nwFirmId', $nwFirmId)
            ->with('nwFirmName', $nwFirmName)
            ->with('status', $status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tcMappingRulesMyModel = new TcMappingRuleMyModel();
        $allItems= $tcMappingRulesMyModel->get(['nw_firm_id']);
        $existNwFirmIds = [];
        foreach ($allItems as $item){
            $existNwFirmIds[] = $item['nw_firm_id'];
        }

        $networkFirm = new NetworkFirmMyModel();
        $firm = $networkFirm->query()
            ->whereNotIn('id', $existNwFirmIds)
            ->get();

        return view('tc-mapping-rule.create')
            ->with('statusMap', $tcMappingRulesMyModel->getStatusMap())
            ->with('firm', $firm)
            ->with('status', TcMappingRuleMyModel::STATUS_ACTIVE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkAccessPermission('tc_mapping_rules_store');

        $nwFirmId = $request->input("nw_firm_id");
        $tcMappingRuleMyModel = new TcMappingRuleMyModel();
        $query = $tcMappingRuleMyModel->queryBuilder();

        if($query->where('nw_firm_id', $nwFirmId)->exists()){
            exit('error.');
        }

        $className = $request->input("class_name");
        $status = $request->input("status");

        if ($className) {
            $className = json_encode(explode(TcMappingRuleMyModel::DA_KEY_SEP, $request->input("class_name", [])));
        } else {
            $className = "[]";
        }

        $data = [
            'nw_firm_id' => $nwFirmId,
            'class_name' => $className,
            'status' => $status,
            'manager' => Auth::id(),
            'update_time' => date('Y-m-d H:i:s'),
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $tcMappingRuleMyModel->queryBuilder()->insert($data);

        return redirect("tc-mapping-rule");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('tc-mapping-rule.show')
            ->with('statusMap', (new TcMappingRuleMyModel())->getStatusMap())
            ->with("data", $this->getOneItem($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkAccessPermission('tc_mapping_rules_update');
        $tcMappingRuleMyModel = new TcMappingRuleMyModel();

        $nwFirmId = $request->input("nw_firm_id");
        $className = $request->input("class_name");
        $status = $request->input("status");

        if ($className) {
            $className = json_encode(explode(TcMappingRuleMyModel::DA_KEY_SEP, $request->input("class_name", [])));
        } else {
            $className = "[]";
        }

        $updateData = [
            'nw_firm_id' => $nwFirmId,
            'class_name' => $className,
            'status' => $status,
            'manager' => Auth::id(),
            'update_time' => date('Y-m-d H:i:s'),
        ];

        $tcMappingRuleMyModel->queryBuilder()->where("id", $id)->update($updateData);

        return redirect("tc-mapping-rule");
    }

    private function unserializeItem($srcItem, $statusMap, $implode = false)
    {
        $item = $srcItem;
        $item['status_name'] = $statusMap[$srcItem['status']];
        $item['admin_name'] = Users::getName($srcItem['manager']);

        if($implode){
            $item['class_name'] = implode(TcMappingRuleMyModel::DA_KEY_SEP, json_decode( $item['class_name']));
        } else {
            $item['class_name'] = json_decode( $item['class_name']);
        }

        return $item;
    }

    private function getOneItem($id)
    {
        $oneRow = DB::table('tc_mapping_rule as tmr')
            ->leftJoin('network_firm as nwf', 'nwf.id', '=', 'tmr.nw_firm_id')
            ->select('tmr.*', 'nwf.name as nw_firm_name')
            ->where("tmr.id", $id)
            ->first();

        return $this->unserializeItem($oneRow, (new TcMappingRuleMyModel())->getStatusMap(), true);
    }
}
