<?php

namespace App\Http\Controllers;

use App\Models\MySql\App as AppMyModel;
use App\Models\MySql\TcUploadRules as TcUploadRulesMyModel;
use App\Models\MySql\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TcUploadRulesController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('tc_upload_rules');

        $appUuid = $request->input('app_uuid', '');
        $appName = $request->input('app_name', '');
        $status = $request->input('status', 'all');

        $tcUploadRulesMyModel = new TcUploadRulesMyModel();
        $query = DB::table('tc_upload_rules as tur')
            ->leftJoin('app', 'app.id', '=', 'tur.app_id')
            ->select('tur.id as id', 'tur.create_time as create_time', 'tur.update_time as update_time',
                'tur.*',
                'tur.status as status', 'tur.manager as manager', 'tur.app_id as app_id',
                'tur.update_time as update_time',
                'app.name as app_name', 'app.uuid as app_uuid');
        if ($appUuid) {
            $query->where('app.uuid', $appUuid);
        }
        if ($appName) {
            $query->where('app.name', 'like', "%{$appName}%");
        }
        if ($status != "all" && in_array($status, array_keys($tcUploadRulesMyModel->getStatusMap()))) {
            $query->where('tur.status', $status);
        } else {
            $query->where('tur.status', '>', -1);
        }

        $data = $query->orderBy('tur.id', 'desc')->paginate(10);
        $statusMap = $tcUploadRulesMyModel->getStatusMap();
        foreach ($data as $key => $val) {
            $data->put($key, $this->unserializeItem($val, $statusMap));
        }

        return view('tc-upload-rules.index')
            ->with('data', $data)
            ->with('statusMap', $tcUploadRulesMyModel->getStatusMap())
            ->with('appUuid', $appUuid)
            ->with('appName', $appName)
            ->with('status', $status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tcUploadRulesMyModel = new TcUploadRulesMyModel();
        $data = [
            'wdlst' => implode(TcUploadRulesMyModel::DA_KEY_SEP, array('itunes.apple.com', 'it.apple.com', 'apps.apple.com')),
            'status' => TcUploadRulesMyModel::STATUS_ACTIVE
        ];
        return view('tc-upload-rules.create')
            ->with('statusMap', $tcUploadRulesMyModel->getStatusMap())
            ->with("data", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkAccessPermission('tc_upload_rules_store');

        $tcUploadRulesMyModel = new TcUploadRulesMyModel();
        $appUuid = $request->input("app_uuid");
        $tcSwitch = $request->input("tc_switch");
        $collectWebviewSwitch = $request->input("collect_webview_switch");
        $collectStorekitSwitch = $request->input("collect_storekit_switch");
        $collectOpenUrlSwitch = $request->input("collect_open_url_switch");
        $classNameBlackList = $request->input("cnslst", "");///类名黑名单
        $domainNameWhiteList = $request->input("wdlst", "");///URL域名白名单
        if ($classNameBlackList) {
            $classNameBlackList = json_encode(explode(TcUploadRulesMyModel::DA_KEY_SEP, $request->input("cnslst", [])));
        } else {
            $classNameBlackList = "[]";
        }
        if ($domainNameWhiteList) {
            $domainNameWhiteList = json_encode(explode(TcUploadRulesMyModel::DA_KEY_SEP, $request->input("wdlst", [])));
        } else {
            $domainNameWhiteList = "[]";
        }

        $status = $request->input("status");

        if ($appUuid) {
            $oneApp = (new AppMyModel())->queryBuilder()->where("uuid", $appUuid)->where("status", ">", 0)->first();
            if (empty($oneApp)) {
                exit("could not find the app");
            }
            $appId = $oneApp['id'];
            if($this->isItemExist(0, $appId)){
                exit("app already exist");
            }
        } else {
            $appId = 0;
            if ($this->isItemExist(0, $appId)) {
                exit("the default rule could only be one");
            }
            $status = TcUploadRulesMyModel::STATUS_ACTIVE;
        }

        $data = [
            'app_id' => $appId,
            'domain_name_white_list' => $domainNameWhiteList,
            'class_name_black_list' => $classNameBlackList,
            'collect_openurl_switch' => $collectOpenUrlSwitch,
            'collect_storekit_apple_id_switch' => $collectStorekitSwitch,
            'collect_webview_url_switch' => $collectWebviewSwitch,
            'tc_main_switch' => $tcSwitch,
            'status' => $status,
            'manager' => Auth::id(),
            'update_time' => date('Y-m-d H:i:s'),
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $tcUploadRulesMyModel->queryBuilder()->insert($data);

        return redirect("tc-upload-rule");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('tc-upload-rules.show')
            ->with('statusMap', (new TcUploadRulesMyModel())->getStatusMap())
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
        $this->checkAccessPermission('tc_upload_rules_update');
        $strategyAppLoggerMyModel = new TcUploadRulesMyModel();

        $tcSwitch = $request->input("tc_switch");
        $collectWebviewSwitch = $request->input("collect_webview_switch");
        $collectStorekitSwitch = $request->input("collect_storekit_switch");
        $collectOpenUrlSwitch = $request->input("collect_open_url_switch");
        $classNameBlackList = $request->input("cnslst", "");///类名黑名单
        $domainNameWhiteList = $request->input("wdlst", "");///URL域名白名单
        if ($classNameBlackList) {
            $classNameBlackList = json_encode(explode(TcUploadRulesMyModel::DA_KEY_SEP, $request->input("cnslst", [])));
        } else {
            $classNameBlackList = "[]";
        }
        if ($domainNameWhiteList) {
            $domainNameWhiteList = json_encode(explode(TcUploadRulesMyModel::DA_KEY_SEP, $request->input("wdlst", [])));
        } else {
            $domainNameWhiteList = "[]";
        }

        $status = $request->input("status");
        $appUuid = $request->input("app_uuid");

        if ($appUuid) {
            $oneApp = (new AppMyModel())->queryBuilder()->where("uuid", $appUuid)->first();
            if (empty($oneApp)) {
                exit("could not find the app");
            }
            $appId = $oneApp['id'];
            if($this->isItemExist($id, $appId)){
                exit("app already exist");
            }
        } else {
            $appId = 0;
            if ($this->isItemExist($id, $appId)) {
                exit("the default rule could only be one");
            }
            $status = TcUploadRulesMyModel::STATUS_ACTIVE;
        }

        $updateData = [
            'app_id' => $appId,
            'domain_name_white_list' => $domainNameWhiteList,
            'class_name_black_list' => $classNameBlackList,
            'collect_openurl_switch' => $collectOpenUrlSwitch,
            'collect_storekit_apple_id_switch' => $collectStorekitSwitch,
            'collect_webview_url_switch' => $collectWebviewSwitch,
            'tc_main_switch' => $tcSwitch,
            'status' => $status,
            'manager' => Auth::id(),
            'update_time' => date('Y-m-d H:i:s'),
        ];

        $strategyAppLoggerMyModel->queryBuilder()->where("id", $id)->update($updateData);

        return redirect("tc-upload-rule");
    }

    public function copyOne($id)
    {
        return view('tc-upload-rules.copy_one')
            ->with('statusMap', (new TcUploadRulesMyModel())->getStatusMap())
            ->with("data", $this->getOneItem($id));
    }

    private function unserializeItem($srcItem, $statusMap, $serializeSwitch = true, $implode = false)
    {
        $item = $srcItem;
        if ($item['app_id'] == 0) {
            $item['app_id'] = "";
            $item['app_uuid'] = 0;
            $item['app_name'] = "-";
        }

        $item['status_name'] = $statusMap[$srcItem['status']];
        $item['admin_name'] = Users::getName($srcItem['manager']);

        if($implode){
            $item['domain_name_white_list'] = implode(TcUploadRulesMyModel::DA_KEY_SEP, json_decode( $item['domain_name_white_list']));
            $item['class_name_black_list'] = implode(TcUploadRulesMyModel::DA_KEY_SEP, json_decode( $item['class_name_black_list']));
        } else {
            $item['domain_name_white_list'] = json_decode( $item['domain_name_white_list']);
            $item['class_name_black_list'] = json_decode( $item['class_name_black_list']);
        }

        if($serializeSwitch){
            $item['collect_openurl_switch'] = $item['collect_openurl_switch'] . "%";
            $item['collect_storekit_apple_id_switch'] = $item['collect_storekit_apple_id_switch'] . "%";
            $item['collect_webview_url_switch'] = $item['collect_webview_url_switch'] . "%";
            $item['tc_main_switch'] = $item['tc_main_switch'] . "%";
        }


        return $item;
    }

    private function getOneItem($id)
    {
        $oneRow = DB::table('tc_upload_rules as tur')
            ->leftJoin('app', 'app.id', '=', 'tur.app_id')
            ->select('tur.*', 'app.name as app_name', 'app.uuid as app_uuid')
            ->where("tur.id", $id)
            ->first();

        return $this->unserializeItem($oneRow, (new TcUploadRulesMyModel())->getStatusMap(), false, true);
    }

    private function isItemExist($id = 0, $appId = 0)
    {
        $strategyAppLoggerMyModel = new TcUploadRulesMyModel();
        $query = $strategyAppLoggerMyModel->queryBuilder();
        if ($id != 0) {
            $query = $query->where("id", "!=", $id);
        }

        return $query->where("app_id", $appId)->exists();
    }
}
