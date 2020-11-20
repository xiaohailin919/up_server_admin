<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

use App\Models\MySql\Network;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\PublisherApiInfo as PublisherApiInfoMyModel;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\MySql\Users;
use App\Models\MySql\PublisherGroup;
use App\Models\MySql\PublisherGroupRelationship;

use App\Events\UpdatePublisher;
use App\Services\Mail;
use App\Helpers\Export;
use App\Helpers\ArrayUtil;
use App\Utils\ChannelAdapter;
use App\Utils\SHAHasher;

use Grpc;
use Illuminate\View\View;
use Proto\PublisherId;
use Proto\RpcWaterFallServiceClient;


class PublisherController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('publisher_list');
        
        $publisherId   = $request->input('publisher_id', '');
        $publisherName = $request->input('publisher_name', '');
        $publisherType = $request->input('publisher_type', 'all');
        $system        = $request->input('system', 'all');
        $note          = $request->input('note', '');
        $email         = $request->input('email', '');
        $export        = $request->input('export', 0);
        $searchType    = $request->input('search_type', 'main');
        $channel = $request->input('channel', 'all');
        if($channel != 'all'){
            $channel = (int)$channel;
        }
        $status        = $request->input('status', 'all');
        if($status != 'all'){
            $status = (int)$status;
        }

        /* 如果请求参数包含 publisher_group，则加一层筛选 | dev20200602 - zohar */
        $publisherGroupIds = $request->input('publisher_group_ids', []);
        $publisherIds = [];
        if ($publisherGroupIds !== []) {
            $publisherGroupRelationships = PublisherGroupRelationship::query()
                ->whereIn('publisher_group_id', $publisherGroupIds)
                ->get(['publisher_id'])
                ->toArray();
            $publisherIds = array_column($publisherGroupRelationships, 'publisher_id');
        }

        $adminId = Auth::id();
        $publisherMyModel = new PublisherMyModel();

        if($searchType == 'sub'){
            $parentIds = $this->indexQueryBuilder($request)
                ->select('sub_account_parent')
                ->where('sub_account_parent', '>', 0)
                ->get()
                ->toArray();
            $parentIds = array_unique(array_column($parentIds, 'sub_account_parent'));

            $query = $publisherMyModel->queryBuilder();
            $publisher = $query->whereIn('id', $parentIds);
        }else{
            $query = $this->indexQueryBuilder($request);
            ChannelAdapter::adaptPublisherQueryBuilder($query, $adminId);
            $publisher = $query->where('sub_account_parent', 0);
        }

        if ($publisherIds !== []) {
            $publisher->whereIn('id', $publisherIds);
        }

        $publisher = $publisher->orderByDesc('create_time');

        if($export) {
            $header = [
                "id" => "Publisher ID",
                "name" => "Name",
                "email" => "Email",
                "channel_id" => "Channel",
                "create_time" => "Create Time",
                "status" => "Status",
                "note" => "Note",
                "company" => "Company",
                'contact'=> "Contact",
                'phone_number'=> "Phone Number",
                'wechat'=> "WeChat",
                'qq'=> "QQ",
                'skype'=> "Skype",
                "note_channel" => "Channel Note"
            ];
//            if(ChannelAdapter::isInChannelList($adminId)){
//                $header = [
//                    "id" => "Publisher ID",
//                    "name" => "Name",
//                    "email" => "Email",
//                    "create_time" => "Create Time",
//                    "status" => "Status",
//                    "note_channel" => "Channel Note"
//                ];
//            }

            $publisherList = $publisher->get()->toArray();
            $statusMap = (new PublisherMyModel())->getStatusMap();
            $channelMap = (new PublisherMyModel())->getChannelMap();
            foreach ($publisherList as $key => $val){
                $publisherList[$key]['status'] = $statusMap[$val['status']];
                $publisherList[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                foreach($val as $field => $fieldVal){
                   if($field != "channel_id" && (empty($fieldVal) || !$fieldVal)){
                       $publisherList[$key][$field] = "-";
                   }

                   if($field == "phone_number" && $fieldVal != ""){
                       $publisherList[$key][$field] = str_replace(["-", '+'], " ", $publisherList[$key][$field]);
                   }

                   if($field == "channel_id"){
                       $publisherList[$key][$field] = $channelMap[$fieldVal];
                   }
                }
            }

            Export::excel($header, $publisherList);
            exit;
        }

        $publisher = $publisher->paginate(20);

        $timestamp = time();

        $adminId = Auth::id();
        $publisherIds = [];
        foreach($publisher as $key => $val){
            $publisherIds[] = $val['id'];
            $tmp = $val;
            $tmp['note_title'] = "";
            $tmp['note_channel_title'] = "";
            if(strlen($val['note']) > 12){
                $tmp['note_title'] = $val['note'];
                $tmp['note'] =  mb_substr($tmp['note'], 0, 12, "utf-8") . "...";
            }
            if(strlen($val['note_channel']) > 12){
                $tmp['note_channel_title'] = $val['note_channel'];
                $tmp['note_channel'] =  mb_substr($tmp['note_channel'], 0, 12, "utf-8") . "...";
            }

            $tmp['admin_name'] = Users::getName($tmp['admin_id']);
            $tmp['channel_name'] = $publisherMyModel->getChannelName($tmp['channel_id']);

            /* 增加 Publisher Group 信息, 如果设置 PublisherGroup 参数，对数据进行过滤 | dev20200602 zohar */
            $tmp['publisher_group_ids'] = [];
            $publisherGroupRelationships = PublisherGroupRelationship::query()
                ->where('publisher_id', $val['id'])->get();
            foreach ($publisherGroupRelationships as $publisherGroupRelationship) {
                $tmp['publisher_group_ids'][] = $publisherGroupRelationship['publisher_group_id'];
            }

            /* 应用修改 */
            $publisher->put($key, $tmp);
        }

        // 当前分页下Publisher的子账号数据
        $publisher = $this->fillSubPublisher($request, $publisherIds, $publisher, ($searchType != 'sub'));

//        return $publisher->toArray();

        return view(ChannelAdapter::getViewFile(ChannelAdapter::PAGE_PUBLISHER_INDEX, Auth::id()))
            ->with('publisher', $publisher)
            ->with('publisherGroupIdNameMap', (new PublisherGroup())->getPublisherGroupIdNameMap())
            ->with('statusMap', $publisherMyModel->getStatusMap(true))
            ->with('channelMap', $publisherMyModel->getChannelMap())
            ->with('publisherId', $publisherId)
            ->with('publisherName', $publisherName)
            ->with('email', $email)
            ->with('publisherType', $publisherType)
            ->with('publisherTypeMap', $publisherMyModel->getPublisherTypeMap())
            ->with('systemMap', $publisherMyModel->getSystemMap())
            ->with('system', $system)
            ->with('note', $note)
            ->with('status', $status)
            ->with('searchType', $searchType)
            ->with('channel', $channel)
            ->with('publisherGroupIds', $publisherGroupIds);
    }

    public function edit(Request $request, $id){
        $this->checkAccessPermission('publisher_edit');

        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder();
        ChannelAdapter::adaptPublisherQueryBuilder($query, Auth::id());
        $data = $query->where('id', $id)->first();
        if($data === null){
            exit("publisher not exists");
        }

        /* 初始化 publisher_key、API 权限开关、Device 维度数据开关 */
        $data['publisher_key'] = "";
        $data['api_switch'] = $data['mode'] == Publisher::MODE_WHITE ? Publisher::API_SWITCH_ON : Publisher::API_SWITCH_OFF;
        $data['device_data_switch'] = PublisherMyModel::DEVICE_DATA_SWITCH_OFF;

        $publisherApiInfoMyModel = new PublisherApiInfoMyModel();
        $publisherApiInfo = $publisherApiInfoMyModel->where("publisher_id", $data['id'])->first();
        if(!empty($publisherApiInfo)){
            $data['publisher_key'] = $publisherApiInfo['publisher_key'];
            $data['api_switch'] = $publisherApiInfo['up_api_permission'];
            $data['device_data_switch'] = $publisherApiInfo['up_device_permission'];
        }
        /* 间接刷历史数据，数据错误当其他地方会用 mode 过滤所以不会带来太大影响，所以在每次修改的时候对数据进行修正：所有黑盒开发者，API 权限都关掉 */
        if ($data['mode'] == Publisher::MODE_BLACK) {
            $data['api_switch'] = Publisher::API_SWITCH_OFF;
            $data['device_data_switch'] = Publisher::DEVICE_DATA_SWITCH_OFF;
        }

        $migrateStatusMap = $publisherMyModel->getMigrateStatusMap();
//        unset($migrateStatusMap[Publisher::MIGRATE_STATUS_DOING]);
        $canMigrate = ($data['migrate_4'] == Publisher::MIGRATE_STATUS_ORIGINAL);

        // allow firms
        if(!empty($data['allow_firms'])){
            $data['allow_firms'] = json_decode($data['allow_firms'], true);
        }else{
            $data['allow_firms'] = PublisherMyModel::DEFAULT_ALLOW_FIRMS;
        }
        $data['allow_firms_count'] = count($data['allow_firms']);
        $data['firms_count'] = NetworkFirm::query()
            ->where('publisher_id', 0)
            ->where('id', '!=', NetworkFirm::MYOFFER)
            ->count();

        $data['admin_name'] = Users::getName($data['admin_id']);

//        $view = 'publisher.edit';
        if($data['sub_account_parent'] > 0){
            $view = 'publisher.edit-sub-publisher';
        } else {
            $view = ChannelAdapter::getViewFile(ChannelAdapter::PAGE_PUBLISHER_EDIT, Auth::id());
        }

        /* 处理 Publisher Group */
        $data['publisher_group_ids'] = [];
        $publisherGroups = PublisherGroupRelationship::query()->where('publisher_id', $data['id'])->get();
        foreach ($publisherGroups as $publisherGroup) {
            $data['publisher_group_ids'][] = $publisherGroup['publisher_group_id'];
        }

        return view($view)
            ->with('data', $data)
            ->with('statusMap', $publisherMyModel->getStatusMap())
            ->with('migrateStatusMap', $migrateStatusMap)
            ->with('canMigrate', $canMigrate)
            ->with('publisherGroupIdNameMap', (new PublisherGroup())->getPublisherGroupIdNameMap())
            ->with('apiSwitchMap', $publisherMyModel->getApiSwitchMap())
            ->with('deviceDataSwitchMap', $publisherMyModel->getDeviceDataSwitchMap())
            ->with('reportImportSwitchMap', $publisherMyModel->getReportImportSwitchMap())
            ->with('currencyMap', $publisherMyModel->getCurrencyMap())
            ->with('modeMap', Publisher::getPublisherTypeMap())
            ->with('myOfferSwitchMap', $publisherMyModel->getMyOfferSwitchMap())
            ->with('unitRepeatSwitchMap', $publisherMyModel->getUnitRepeatSwitchMap())
            ->with('subAccountSwitchMap', $publisherMyModel->getSubAccountSwitchMap())
            ->with('distributionSwitchMap', $publisherMyModel->getDistributionSwitchMap())
            ->with('networkMultipleSwitchMap', $publisherMyModel->getNetworkMultipleSwitchMap())
            ->with('reportTimezoneSwitchMap', $publisherMyModel->getReportTimezoneSwitchMap())
            ->with('scenarioSwitchMap', $publisherMyModel->getScenarioSwitchMap())
            ->with('adxSwitchMap', $publisherMyModel->getAdxSwitchMap())
            ->with('adxUnitSwitchMap', $publisherMyModel->getAdxUnitSwitchMap());
    }
    
    public function update(Request $request, $id)
    {
        $this->checkAccessPermission('publisher_edit');

        $publisherMyModel = new PublisherMyModel();

        $mode                  = $request->input('mode', PublisherMyModel::MODE_WHITE);
        $status                = $request->input('status', PublisherMyModel::STATUS_LOCKED);
        $currency              = $request->input('currency', 'USD');
        $publisherGroupIds     = $request->input('publisher_group_ids', []);
        $apiSwitch             = $request->input('api_switch', PublisherMyModel::API_SWITCH_OFF);
        $deviceDataSwitch      = $request->input('device_data_switch', PublisherMyModel::DEVICE_DATA_SWITCH_OFF);
        $reportImportSwitch    = $request->input('report_import_switch', PublisherMyModel::REPORT_IMPORT_SWITCH_OFF);
        $myOfferSwitch         = $request->input('my_offer_switch', PublisherMyModel::MY_OFFER_SWITCH_OFF);
        $company               = (string)$request->input('company', "");
        $contact               = (string)$request->input('contact', "");
        $phoneNumber           = (string)$request->input('phone_number', "");
        $note                  = (string)$request->input('note', "");
        $noteChannel           = (string)$request->input('note_channel', "");
        $version               = $request->input("version", Publisher::MIGRATE_STATUS_ORIGINAL);
        $unitRepeatSwitch      = $request->input("unit_repeat_switch", Publisher::UNIT_REPEAT_SWITCH_OFF);
        $subAccountSwitch      = $request->input("sub_account_switch", Publisher::SUB_ACCOUNT_SWITCH_OFF);
        $subAccountDistSwitch  = $request->input("distribution_switch", Publisher::DIST_SWITCH_OFF);
        $networkMultipleSwitch = $request->input("network_multiple_switch", Publisher::NETWORK_MULTIPLE_SWITCH_OFF);
        $reportTimezoneSwitch  = $request->input("report_timezone_switch", Publisher::REPORT_TIMEZONE_SWITCH_OFF);
        $scenarioSwitch        = $request->input("scenario_switch", Publisher::SCENARIO_SWITCH_OFF);
        $adxSwitch             = $request->input("adx_switch", Publisher::ADX_SWITCH_OFF);
        $adxUnitSwitch         = $request->input("adx_unit_switch", Publisher::ADX_UNIT_SWITCH_OFF);

        if(!array_key_exists($reportTimezoneSwitch, $publisherMyModel->getReportTimezoneSwitchMap())){
            $reportTimezoneSwitch = Publisher::REPORT_TIMEZONE_SWITCH_OFF;
        }

        $query = Publisher::query();

        $publisher = $oneUser = $query->where('id', $id)->first();
        if($publisher === null){
            exit("publisher not exists");
        }

        /* 处理用户群组 PublisherGroup */
        $publisherGroupRelationships = PublisherGroupRelationship::query()->where('publisher_id', $publisher['id'])->get();
        foreach ($publisherGroupRelationships as $publisherGroupRelationship) {
            if (in_array($publisherGroupRelationship['publisher_group_id'], $publisherGroupIds, false)) {
                continue;
            }
            $publisherGroupRelationship->delete();
        }
        foreach ($publisherGroupIds as $publisherGroupId) {
            PublisherGroupRelationship::query()->firstOrCreate(['publisher_group_id' => $publisherGroupId, 'publisher_id' => $publisher['id']]);
        }

        $data = [
            'admin_id' => Auth::id(),
            'update_time' => time(),
        ];
        $migrate = false;
        if($version == Publisher::MIGRATE_STATUS_FINISH && $oneUser['migrate_4'] == Publisher::MIGRATE_STATUS_ORIGINAL){
            $migrate = true;
        }

        // 如果 报表时区 关闭
        if($reportTimezoneSwitch != $publisher['report_timezone_switch']){
            $data['report_timezone'] = 108;
        }

        // 白盒/黑盒
        if($mode){
            $data['mode'] = $mode;
            if($mode == Publisher::TYPE_BLACK){
                $data['report_timezone_switch'] = Publisher::REPORT_TIMEZONE_SWITCH_OFF;
                $data['report_timezone']        = 0;
            }
        }

        /* 所有黑盒开发者，API 权限都关掉；所有 API 权限关闭的，设备权限也关闭 */
        if($mode == PublisherMyModel::MODE_BLACK) {
            $apiSwitch = PublisherMyModel::API_SWITCH_OFF;
            $reportImportSwitch = PublisherMyModel::REPORT_IMPORT_SWITCH_OFF;
            $deviceDataSwitch = PublisherMyModel::DEVICE_DATA_SWITCH_OFF;
        }
        if($apiSwitch == PublisherMyModel::API_SWITCH_OFF){
            $deviceDataSwitch = PublisherMyModel::DEVICE_DATA_SWITCH_OFF;
        }

        $data['report_import_switch']   = $reportImportSwitch;
        $data['report_timezone_switch'] = $reportTimezoneSwitch;
        $data['scenario_switch']        = $scenarioSwitch;
        $data['adx_switch']             = $adxSwitch;
        $data['adx_unit_switch']        = $adxUnitSwitch;
        $data['note']                   = $note;
        $data['company']                = $company;
        $data['contact']                = $contact;
        $data['phone_number']           = $phoneNumber;
        if($status){
            $data['status'] = $status;
            if(in_array($status, [Publisher::STATUS_LOCKED, Publisher::STATUS_PENDING])){
                $publisherMyModel->updateStatusByParentId($id, $status);
            }
        }
        if($currency && $oneUser['currency'] == 'USD'){
            $data['currency'] = strtoupper($currency);
        }
        if($unitRepeatSwitch){
            $data['unit_repeat_switch'] = strtoupper($unitRepeatSwitch);
        }

        // My Offer 开关
        if($myOfferSwitch){
            $data['my_offer_switch'] = $myOfferSwitch;
            if($myOfferSwitch == Publisher::MY_OFFER_SWITCH_ON){
                $publisherId = $id;
                $networkQuery = (new Network())->queryBuilder();
                if(!$networkQuery->where("publisher_id", $publisherId)->where("nw_firm_id", 35)->exists()){
                    $oneNetwork = [
                        'name' => "My Offer",
                        'api_version' => 1,
                        'status' => 3,
                        'app_id' => 0,
                        'open_api_status' => 3,
                        'update_time' => time(),
                        'unit_switch' => 2,
                        'publisher_id' => $publisherId,
                        'nw_firm_id' => 35,
                        'create_time' => time(),
                    ];

                    $networkQuery->insert($oneNetwork);
                }
            }
        }
        if($subAccountSwitch){
            $data['sub_account_switch'] = $subAccountSwitch;
            if($subAccountSwitch == Publisher::SUB_ACCOUNT_SWITCH_OFF){
                $publisherMyModel->updateStatusByParentId($id, Publisher::STATUS_LOCKED);
            }
        }
        if($subAccountSwitch == Publisher::SUB_ACCOUNT_SWITCH_OFF){
            $subAccountDistSwitch = Publisher::DIST_SWITCH_OFF;
        }
        $data['distribution_switch'] = $subAccountDistSwitch;
        if($networkMultipleSwitch){
            $data['network_multiple_switch'] = $networkMultipleSwitch;
        }

        $query->where('id', $id)->update($data);

        # 联动操作

        $publisherApiInfoModel = new PublisherApiInfoMyModel();
        $apiRecord = $publisherApiInfoModel->where("publisher_id", $id)->first();
        if(empty($apiRecord)){
            if($apiSwitch == PublisherMyModel::API_SWITCH_ON){
                $publisherKey = (new SHAHasher())->make(microtime().mt_rand());
                $data = [
                    'publisher_id' => $id,
                    'publisher_key' => $publisherKey,
                    'up_api_permission' => $apiSwitch,
                    'up_device_permission' => $deviceDataSwitch,
                    'update_time' => date('Y-m-d H:i:s'),
                    'create_time' => date('Y-m-d H:i:s'),
                ];
                $publisherApiInfoModel->create($data);
            }
        } else {
            $updateData = [
                'up_api_permission' => $apiSwitch,
                'up_device_permission' => $deviceDataSwitch,
                'update_time' => date('Y-m-d H:i:s')
            ];
            $publisherApiInfoModel->where("publisher_id", $id)->update($updateData);
        }

        //触发更新publisher的事件
        Event::fire(new UpdatePublisher($id, $data));

        if($migrate){
            Log::info("will migrate publisher to new version");
            $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
                'credentials' => Grpc\ChannelCredentials::createInsecure()
            ]);
            $args = new PublisherId(array(
                'id' => $id
            ));
            $res = $client->AdminMigratePublisherData($args)->wait();
            if ($res[1]->code !== 0) {
                Log::info("migrate failed:" . $res[1]->details);
            } else {
                Log::info("the publisher is migrating");
            }
        }

        if($request->ajax()){
            return ["status" => 1];
        }
        return redirect("publisher");
    }

    public function updateSubPublisher(Request $request, $id){
        $this->checkAccessPermission('publisher_edit');

        $status = $request->input('status', PublisherMyModel::STATUS_LOCKED);

        $data = [
            'status' => $status,
        ];
        Publisher::query()->where('id', $id)->update($data);

        // 触发更新publisher的事件
        Event::fire(new UpdatePublisher($id));

        return redirect("publisher");
    }

    public function activate(Request $request)
    {
        $this->checkAccessPermission('publisher_activate');

        $id = $request->input('id', 1);

        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder();

        $data = [
            'admin_id' => Auth::id(),
            'update_time' => time(),
            'status'  => 3,
        ];

        $query->where('id', $id)->update($data);

        //触发更新publisher的事件
        Event::fire(new UpdatePublisher($id));

        $onePublisher = $publisherMyModel->getOne([], ['id' => $id]);
        Mail::toPublisherEmail($onePublisher);

        if($request->ajax()){
            return ["status" => 1];
        }
        return redirect("publisher");
    }

    /**
     * 修改开发者为黑盒模式
     * @param Request $request
     * @return array
     */
    public function updateMode(Request $request)
    {
        $this->checkAccessPermission('publisher_edit');

        $publisherMyModel = new PublisherMyModel();
        $id = $request->input('id', 0);
        $mode = $request->input('mode', 0);

        if(!in_array($mode, array_keys($publisherMyModel->getPublisherTypeMap()))) {
            return [
                "status" => 0,
                "msg" => "unknow mode"
            ];
        }

        $publisher = $publisherMyModel->getOne([], ['id' => $id]);
        if(!$publisher){
            return [
                "status" => false,
                "msg" => "could not find the publisher"
            ];
        }

        $publisherMyModel->queryBuilder()
            ->where('id', $id)
            ->update(['mode'=> $mode]);

        /* 所有黑盒开发者，API 权限都关掉；所有 API 权限关闭的，设备权限也关闭 */
        if ($mode == Publisher::MODE_BLACK) {
            PublisherApiInfoMyModel::query()->where('publisher_id', $id)
                ->update([
                    'up_api_permission' => PublisherApiInfoMyModel::UP_API_PERMISSION_OFF,
                    'up_device_permission' => PublisherApiInfoMyModel::UP_DEVICE_PERMISSION_OFF,
                ]);
        }

        return [
            "status" => 1,
            "msg" => ""
        ];
    }

    /**
     * 通过ID检查Publisher是否存在
     * @param Request $request
     */
    public function checkExist(Request $request){
        $publisherId = $request->input('publisher_id', 0);
        $publisherMyModel = new PublisherMyModel();
        $publisherCheck = $publisherMyModel->queryBuilder()
            ->where('id', $publisherId)
            ->where('status', '>', PublisherMyModel::STATUS_DELETED)
            ->count();

        if($publisherCheck <= 0){
            abort(404);
            return [
                'code' => 1
            ];
        }
        return [
            'code' => 0
        ];
    }

    /**
     * 登录某个用户开发者后台
     * @param Request $request
     */
    public function login(Request $request){
        $id        = $request->input('id', 0);
        $type      = $request->input('type', '');
        $redirect  = $request->input('redirect', '');
        $env       = $request->input('env', '');

        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder();
        $publisher = $query->where('id', $id)->first();
        $email = $publisher['email'];
        $channelId = $publisher['channel_id'];

        if($id <= 0 || !$email){
            return 'error';
        }

        $adminId = Auth::id();
        if($type == 'self'){
            $adminId = 0;
        }

        $timestamp = time();
        $loginParam = [
            "admin_id"  => $adminId,
            "email"     => $email,
            "timestamp" => $timestamp,
            "sign"      => md5($adminId . '/' . $email . '/' . $timestamp . '/' . env('LOGIN_TOKEN_FOR_ADMIN')),
        ];
        if(!empty($redirect)){
            $loginParam['redirect'] = $redirect;
        }

        $url = env('DN_UP_APP');
        if($channelId == Publisher::CHANNEL_233){
            $url = 'http://' . str_replace('admin.', 'app.', env('CHANNEL_233_HOST')) . '/';
        }else if($channelId == Publisher::CHANNEL_DLADS){
            $url = 'http://' . str_replace('admin.', 'app.', env('CHANNEL_DLADS_HOST')) . '/';
        }

        if ($env === 'pre') {
            $url = 'http://pre-app.toponad.com/';
        }
        $loginUrl = $url . 'm/auth-redirect?' . http_build_query($loginParam);

        header("Location: {$loginUrl}");
    }

    /**
     * 管理开发者广告平台页面
     *
     * @param $id
     * @return Factory|Application|View
     */
    public function editAllowFirms($id){
        $data = Publisher::query()->select('id', 'name', 'allow_firms', 'migrate_4')->where('id', $id)->first();
        // allow firms
        if(!empty($data['allow_firms'])){
            $data['allow_firms'] = json_decode($data['allow_firms'], true);
        }else{
            $data['allow_firms'] = PublisherMyModel::DEFAULT_ALLOW_FIRMS;
        }
        $firmsQuery = NetworkFirm::query()
            ->where('publisher_id', 0)
            ->where('id', '!=', NetworkFirm::MYOFFER)
            ->orderBy('rank', 'asc');
        if($data['migrate_4'] < 3){
            $firmsQuery->where('id', '<', 28);
        }
        $firms = $firmsQuery->get(['id', 'name']);

        return view('publisher.edit-allow-firms')
            ->with('data', $data)
            ->with('defaultAllowFirms', Publisher::DEFAULT_ALLOW_FIRMS)
            ->with('firms', $firms);
    }

    /**
     * 更新 allow firms
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function updateAllowFirms(Request $request, $id){
        $allowFirms = (array)$request->input('allow_firms', Publisher::DEFAULT_ALLOW_FIRMS);
        $publisher = new Publisher();

        // 旧版后台只能使用27之前的厂商
        $check = $publisher->queryBuilder()->where('id', $id)->value('migrate_4');
        if($check < 3){
            foreach($allowFirms as $key => $val){
                if($val > 27){
                    unset($allowFirms[$key]);
                }
            }
        }

        $data = [
            'allow_firms' => json_encode($allowFirms),
            'update_time' => time(),
            'admin_id' => Auth::id(),
        ];
        $publisher->queryBuilder()->where('id', $id)->update($data);
        return redirect("publisher");
    }

    private function indexQueryBuilder(Request $request)
    {
        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder();

        $publisherId = $request->input('publisher_id', 0);
        $publisherName = $request->input('publisher_name', '');
        $publisherType = $request->input('publisher_type', 'all');
        $note = $request->input('note', '');
        $status = $request->input('status', 'all');
        $channel = $request->input('channel', 'all');
        $system = $request->input('system', 'all');
        $email = $request->input('email', '');

        if($publisherId > 0){
            $query->where('id', $publisherId);
        }
        if(!empty($publisherName)){
            $query->where('name', 'like', "%{$publisherName}%");
        }
        if(!empty($note)){
            $query->where('note', 'like', "%{$note}%");
        }
        if(in_array($publisherType, array_keys($publisherMyModel->getPublisherTypeMap()))){
            $query->where('mode', $publisherType);
        }
        if($status != 'all' && in_array((int)$status, array_keys($publisherMyModel->getStatusMap(true)))){
            $query->where('status', $status);
        }else{
            $query->where('status', '>', 0);
        }
        if($channel != "all" && in_array((int)$channel, array_keys($publisherMyModel->getChannelMap()), true)){
            $query->where('channel_id', $channel);
        }
        if(in_array($system, array_keys($publisherMyModel->getSystemMap()))){
            $query->where('system', $system);
        }
        if(!empty($email)){
            $query->where('email', 'like', "%{$email}%");
        }

        return $query;
    }

    private function fillSubPublisher(Request $request, $publisherIds, $data, $allSubPublisher = true){
        if(!$allSubPublisher){
            $query = $this->indexQueryBuilder($request);
        }else{
            $publisherMyModel = new PublisherMyModel();
            $query = $publisherMyModel->queryBuilder();
        }
        $subPublisher = $query->where('sub_account_parent', '>', 0)
            ->whereIn('sub_account_parent', $publisherIds)
            ->orderBy('id', 'desc')
            ->get();

        if(empty($subPublisher)){
            return $data;
        }
        $subPublisher = $subPublisher->toArray();

        $adminId   = Auth::id();
        $timestamp = time();
        $channelMap = (new Publisher())->getChannelMap();
        foreach($subPublisher as $key => &$val){
            // 处理子账号数据
            $val['note_title'] = "";
            if(strlen($val['note']) > 12){
                $val['note_title'] = $val['note'];
                $val['note'] =  mb_substr($val['note'], 0, 12, "utf-8") . "...";
            }
            $loginParam = [
                "admin_id" => $adminId,
                "email" =>   $val['email'],
                "timestamp" =>   $timestamp,
                "sign" =>   md5($adminId . '/' .$val['email'] . '/' . $timestamp . '/' . env('LOGIN_TOKEN_FOR_ADMIN')),
            ];
            $selfLoginParam = [
                "admin_id" => 0,
                "email" =>   $val['email'],
                "timestamp" =>   $timestamp,
                "sign" =>   md5(0 . '/' .$val['email'] . '/' . $timestamp . '/' . env('LOGIN_TOKEN_FOR_ADMIN')),
            ];
            $val["login_url"] = env('DN_UP_APP') . "logins?" .http_build_query($loginParam);
            $val["self_login_url"] = env('DN_UP_APP') . "logins?" .http_build_query($selfLoginParam);
            $val['admin_name'] = Users::getName($val['admin_id']);
            $val['channel_name'] = $channelMap[$val['channel_id']];
            /* 增加子账号 Publisher Group 信息 */
            $val['publisher_group_ids'] = [];
            $publisherGroupRelationships = PublisherGroupRelationship::query()->where('publisher_id', $val['id'])->get();
            foreach ($publisherGroupRelationships as $publisherGroupRelationship) {
                $val['publisher_group_ids'][] = $publisherGroupRelationship['publisher_group_id'];
            }
        }

        $subPublisher = ArrayUtil::groupBy($subPublisher, 'sub_account_parent');

        foreach($data as $key => $val){
            $tmp = $val;
            $tmp['sub'] = [];
            if(in_array($val['id'], array_keys($subPublisher))){
                $tmp['sub'] = $subPublisher[$val['id']];
            }
            $data->put($key, $tmp);
        }

        return $data;
    }
}
