<?php

namespace App\Http\Controllers\Channel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

use App\Models\MySql\Network;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\PublisherApiInfo as PublisherApiInfoMyModel;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\MySql\Users;

use App\Events\UpdatePublisher;
use App\Services\Mail;
use App\Helpers\Export;
use App\Helpers\ArrayUtil;
use App\Helpers\Channel;

use Grpc;
use Proto\PublisherId;
use Proto\RpcWaterFallServiceClient;


class PublisherController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkAccessPermission('publisher_list');

        // 初始化全局变量
        $this->roleId    = Channel::getRoleId($this->adminId);
        $this->channelId = Channel::getChannelId($this->roleId);
        
        $publisherId   = $request->input('publisher_id', '');
        $publisherName = $request->input('publisher_name', '');
        $publisherType = $request->input('publisher_type', 'all');
        $system        = $request->input('system', 'all');
        $note          = $request->input('note', '');
        $email         = $request->input('email', '');
        $export        = $request->input('export', 0);
        $searchType    = $request->input('search_type', 'main');
        $channel       = $request->input('channel', 'all');
        if($channel != 'all'){
            $channel = (int)$channel;
        }
        $status        = $request->input('status', 'all');
        if($status != 'all'){
            $status = (int)$status;
        }

        $publisherMyModel = new PublisherMyModel();

        $query = $this->indexQueryBuilder($request)
            ->where('channel_id', $this->channelId);
        $publisher = $query->where('sub_account_parent', 0);

        $publisher = $publisher->orderByDesc('create_time');

        if($export) {
            $header = [
                "id" => "Publisher ID",
                "name" => "Name",
                "email" => "Email",
                "create_time" => "Create Time",
                "status" => "Status",
                "note_channel" => "Channel Note"
            ];

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

        $adminId = Auth::id();
        $publisherIds = [];
        foreach($publisher as $key => $val){
            $publisherIds[] = $val['id'];
            $tmp = $val;
            $tmp['note_title'] = "";
            $tmp['note_channel_title'] = "";
            if(strlen($val['note']) > 12){
                $tmp['note_title'] = $val['note'];
                $tmp['note']       =  mb_substr($tmp['note'], 0, 12, "utf-8") . "...";
            }
            if(strlen($val['note_channel']) > 12){
                $tmp['note_channel_title'] = $val['note_channel'];
                $tmp['note_channel']       =  mb_substr($tmp['note_channel'], 0, 12, "utf-8") . "...";
            }
            $tmp['admin_name'] = Users::getName($tmp['admin_id']);
            $tmp['channel_name'] = $publisherMyModel->getChannelName($tmp['channel_id']);
            $publisher->put($key, $tmp);
        }

        // 当前分页下Publisher的子账号数据
        $publisher = $this->fillSubPublisher($request, $publisherIds, $publisher, ($searchType != 'sub'));

        return view('channel.publisher.index')
            ->with('publisher', $publisher)
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
            ->with('channel', $channel);
    }

    public function edit(Request $request, $id){
        $this->checkAccessPermission('publisher_edit');

        // 初始化全局变量
        $this->roleId    = Channel::getRoleId($this->adminId);
        $this->channelId = Channel::getChannelId($this->roleId);

        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder()
            ->where('channel_id', $this->channelId);
        $data = $query->where('id', $id)->first();
        if(empty($data)){
            exit("publisher not exists");
        }

        $data['publisher_key'] = "";
        $data['api_switch'] = PublisherMyModel::API_SWITCH_OFF;
        $data['device_data_switch'] = PublisherMyModel::DEVICE_DATA_SWITCH_OFF;

        $publisherApiInfoMyModel = new PublisherApiInfoMyModel();
        $publisherApiInfo = $publisherApiInfoMyModel->where("publisher_id", $data['id'])->first();
        if(!empty($publisherApiInfo)){
            $data['publisher_key'] = $publisherApiInfo['publisher_key'];
            $data['api_switch'] = $publisherApiInfo['up_api_permission'];
            $data['device_data_switch'] = $publisherApiInfo['up_device_permission'];
        }
        $migrateStatusMap = $publisherMyModel->getMigrateStatusMap();
//        unset($migrateStatusMap[Publisher::MIGRATE_STATUS_DOING]);
        $canMigrate = ($data['migrate_4'] == Publisher::MIGRATE_STATUS_ORIGINAL);

        // allow firms
        if(!empty($data['allow_firms'])){
            $data['allow_firms'] = (array)json_decode($data['allow_firms']);
        }else{
            $data['allow_firms'] = PublisherMyModel::DEFAULT_ALLOW_FIRMS;
        }
        $data['allow_firms_count'] = count($data['allow_firms']);
        $data['firms_count']       = (new NetworkFirm())->newQuery()->count();

        $data['admin_name'] = Users::getName($data['admin_id']);

        return view('channel.publisher.edit')
            ->with('data', $data)
            ->with('statusMap', $publisherMyModel->getStatusMap())
            ->with('migrateStatusMap', $migrateStatusMap)
            ->with('canMigrate', $canMigrate)
            ->with('apiSwitchMap', $publisherMyModel->getApiSwitchMap())
            ->with('deviceDataSwitchMap', $publisherMyModel->getDeviceDataSwitchMap())
            ->with('reportImportSwitchMap', $publisherMyModel->getReportImportSwitchMap())
            ->with('currencyMap', $publisherMyModel->getCurrencyMap())
            ->with('modeMap', $publisherMyModel->getPublisherTypeMap())
            ->with('myOfferSwitchMap', $publisherMyModel->getMyOfferSwitchMap())
            ->with('unitRepeatSwitchMap', $publisherMyModel->getUnitRepeatSwitchMap())
            ->with('subAccountSwitchMap', $publisherMyModel->getSubAccountSwitchMap())
            ->with('distributionSwitchMap', $publisherMyModel->getDistributionSwitchMap())
            ->with('networkMultipleSwitchMap', $publisherMyModel->getNetworkMultipleSwitchMap())
            ->with('reportTimezoneSwitchMap', $publisherMyModel->getReportTimezoneSwitchMap())
            ->with('scenarioSwitchMap', $publisherMyModel->getScenarioSwitchMap());
    }
    
    public function update(Request $request, $id)
    {
        $this->checkAccessPermission('publisher_edit');

        // 初始化全局变量
        $this->roleId    = Channel::getRoleId($this->adminId);
        $this->channelId = Channel::getChannelId($this->roleId);

        $publisherMyModel = new PublisherMyModel();

        $mode                  = $request->input('mode', PublisherMyModel::MODE_WHITE);
        $status                = $request->input('status', PublisherMyModel::STATUS_LOCKED);
        $currency              = $request->input('currency', 'USD');
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
        if(!in_array($reportTimezoneSwitch, array_keys($publisherMyModel->getReportTimezoneSwitchMap()))){
            $reportTimezoneSwitch = Publisher::REPORT_TIMEZONE_SWITCH_OFF;
        }

        $query = $publisherMyModel->queryBuilder()
            ->where('channel_id', $this->channelId);

        $oneUser = $query->where('id', $id)->first();
        if(empty($oneUser)){
            exit("publisher not exists");
        }

        // channel下的publisher，channel note可随意修改，但是仅当publisher status=Running方可修改status
        $data = [
            'admin_id'     => Auth::id(),
            'update_time'  => time(),
            'note_channel' => (string)$noteChannel,
        ];

        if($oneUser['status'] != PublisherMyModel::STATUS_PENDING && in_array($status, [PublisherMyModel::STATUS_LOCKED, PublisherMyModel::STATUS_RUNNING])) {
            $data['status'] = $status;
        }

        $query->where('id', $id)->update($data);

        return redirect("publisher");
    }

    public function updateSubPublisher(Request $request, $id){
        $this->checkAccessPermission('publisher_edit');

        $status = $request->input('status', PublisherMyModel::STATUS_LOCKED);

        $data = [
            'status' => $status,
        ];
        $publisherMyModel = new PublisherMyModel();
        $query = $publisherMyModel->queryBuilder();
        $query->where('id', $id)->update($data);

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
        if($channelId == 10000){
            $url = 'http://' . str_replace('admin.', 'app.', env('CHANNEL_233_HOST')) . '/';
        }
        $loginUrl = $url . "logins?" .http_build_query($loginParam);

        header("Location: {$loginUrl}");
    }

    /**
     * 编辑 allow firms
     * @param Request $request
     */
    public function editALlowFirms(Request $request, $id){
        $publisher = new Publisher();
        $data = $publisher->queryBuilder()->select('id', 'name', 'allow_firms', 'migrate_4')->where('id', $id)->first();
        // allow firms
        if(!empty($data['allow_firms'])){
            $data['allow_firms'] = (array)json_decode($data['allow_firms']);
        }else{
            $data['allow_firms'] = PublisherMyModel::DEFAULT_ALLOW_FIRMS;
        }
        $firmsQuery = (new NetworkFirm())::query()
            ->whereNotIn('id', [NetworkFirm::MYOFFER])
            ->orderBy('rank', 'asc');
        if($data['migrate_4'] < 3){
            $firmsQuery->where('id', '<', 28);
        }
        $firms = $firmsQuery->get();

        return view('publisher.edit-allow-firms')
            ->with('data', $data)
            ->with('defaultAllowFirms', Publisher::DEFAULT_ALLOW_FIRMS)
            ->with('firms', $firms);
    }

    /**
     * 更新 allow firms
     * @param Request $request
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
