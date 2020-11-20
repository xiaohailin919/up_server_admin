<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

use App\Models\MySql\StrategyApp;
use App\Models\MySql\StrategyApp as StrategyAppMyModel;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\App;

use App\Services\StrategyApp as StrategyAppService;
use App\Events\UpdateApp;


class StrategyAppController extends ApiController
{
    /**
     * App策略 列表
     * http://yapi.toponad.com/project/18/interface/api/761
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
//        $appId    = $request->input('app_id', '');
        $appUuid  = $request->input('app_id', '');
//        $appName  = $request->input('app_name', '');
        $status   = $request->input('status', 'all');
        $system   = $request->input('system', 'all');
        $platform = $request->input('platform', 'all');
        $pageSize = $request->get('page_size', 10);
        $pageNo   = $request->get('page_no', 1);

        $strategyAppMyModel = new StrategyAppMyModel();
        $query = StrategyApp::query()->from('strategy_app as st')
            ->leftJoin('app', 'app.id', '=', 'st.app_id');
        if($appUuid){
            $query->where('app.uuid', $appUuid);
        }
        if(array_key_exists($status, $strategyAppMyModel::getStatusMap())){
            $query->where('st.status', $status);
        }else{
            $query->where('st.status', '>', 0);
        }
        if(array_key_exists($system, $strategyAppMyModel::getSystemMap())){
            $query->where('system', $system);
        }
        if(array_key_exists($platform, $strategyAppMyModel::getPlatformMap())){
            $query->where('platform', $platform);
        }

        $selectList = ['st.id', 'st.status', 'st.create_time', 'st.update_time',
            'app.name as app_name', 'app.uuid as app_uuid', 'app.system as system', 'app.platform as platform'];
        $paginator  = $query->orderBy('st.update_time', 'desc')
            ->paginate($pageSize, $selectList, 'page_no', $pageNo);
        $data       = $this->parseResByPaginator($paginator);

        return $this->jsonResponse($data);
    }

    /**
     * App策略 单条记录
     * http://yapi.toponad.com/project/18/interface/api/770
     *
     * @param  $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $data = StrategyApp::query()->where('id', $id)->firstOrFail();

        $data['network_pre_init_list'] = json_decode($data['network_pre_init_list'], true);
        $data['network_pre_init']      = !empty($data['network_pre_init_list']) ? array_keys($data['network_pre_init_list']) : [];
        $dataLevel                     = json_decode($data['data_level'], true);
        $dataLevelTmp                  = [];
        foreach($dataLevel as $key => $val){
            if($val == 1){
                $dataLevelTmp[] = $key;
            }
        }
        $data['data_level'] = $dataLevelTmp;

        $networkFirmModel = new NetworkFirm();
        $networkFirmList = $networkFirmModel::getNwFirmMap();
        // 只显示可以设置的厂商
        foreach($networkFirmList as $key => $name){
            if(!array_key_exists($key, StrategyAppMyModel::$androidInitAdapter)){
                unset($networkFirmList[$key]);
            }
        }

        /* 处理 Crash 收集包名匹配列表，先转数组再转回字符串 */
        $data['crash_list'] = $data['crash_list'] === '[]' ? [] : json_decode($data['crash_list'], true);
        $data['crash_type'] = 'specific';
        if(empty($data['crash_list'])){
            $data['crash_type'] = 'all';
        }
//        $tmpStr = '';
//        foreach ($data['crash_list'] as $datum) {
//            $tmpStr .= $datum . "\n";
//        }
//        $data['crash_list'] = $tmpStr;

        return $this->jsonResponse($data);
    }

    /**
     * App策略 编辑
     * http://yapi.toponad.com/project/18/interface/api/788
     *
     * @param  Request $request
     * @param  $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function update(Request $request, $id)
    {
        $status = $request->input('status', 1);
        $input = $request->input();

        $appId = StrategyApp::query()->where('id', $id)->value('app_id');
        if($appId <= 0){
            return $this->jsonResponse([], 9999, 'APP ID Error');
        }

        $app = App::query()->where('id', $appId)->firstOrFail();

        if(isset($input['network_pre_init']) && !empty($input['network_pre_init'])){
            $strategyAppService = new StrategyAppService();
            $input['network_pre_init_list'] = json_encode((object)$strategyAppService->genNetworkPreInitList($input['network_pre_init'], $app));
        }else{
            $input['network_pre_init_list'] = '{}';
        }

        // data level
        $dataLevelArray = ['m', 'i', 'a'];
        if(!isset($input['data_level'])){
            $input['data_level'] = [];
        }
        $dataLevel = [];
        foreach($dataLevelArray as $dl){
            if(in_array($dl, $input['data_level'], false)){
                $dataLevel[$dl] = 1;
            }else{
                $dataLevel[$dl] = 0;
            }
        }
//        $dataLevel['m'] = (int)data_get($input['data_level'], 'm', 0);
//        $dataLevel['i'] = (int)data_get($input['data_level'], 'i', 0);
//        $dataLevel['a'] = (int)data_get($input['data_level'], 'a', 0);
        $input['data_level'] = json_encode($dataLevel);

        $data = [
            'status'      => $status,
            'update_time' => time(),
        ];
        $pList = [
            'cache_time',
            'new_psid_time',
            'psid_hot_leave',
            'leave_app_switch',
            'cache_areasize',
            'placement_timeout',
            'gdpr_consent_set',
            'gdpr_server_only',
            'gdpr_notify_url',
            'network_pre_init_list',
            'network_gdpr_switch',
            'notice_list',
            'data_level',
            'req_sw',
            'req_addr',
            'req_tcp_addr',
            'req_tcp_port',
            'bid_sw',
            'bid_addr',
            'tk_sw',
            'tk_addr',
            'tk_tcp_addr',
            'tk_tcp_port',
            'adx_apk_sw',
            'ol_sw',
            'ol_req_addr',
            'ol_tcp_addr',
            'ol_tcp_port',
        ];
        $allowEmpty = [
            'notice_list',
        ];

        foreach ($pList as $p) {
            if (isset($input[$p]) && in_array($p, $allowEmpty)) {
                $data[$p] = (string)$input[$p];
                continue;
            }

            if (isset($input[$p])) {
                $data[$p] = $input[$p];
            }
        }

//        dev20200612 该配置项已废弃
//        foreach (['rf_download', 'rf_install'] as $key) {
//            if (isset($input[$key])) {
//                $data[$key] = $input[$key];
//                if ($data[$key] == 1) {
//                    foreach (['rf_key', 'rf_app_id', 'rf_power', 'rf_power2'] as $k) {
//                        if (isset($input[$k])) {
//                            $data[$k] = $input[$k];
//                        }
//                    }
//                }
//            }
//        }

        /* 处理 Crash 日志 */
        $data['crash_sw'] = (int)$request->get('crash_sw', StrategyApp::CRASH_SWITCH_ON);
        $crashType = $request->get('crash_type', 'all');
        $data['crash_list'] = $request->get('crash_list', []);
        if ($data['crash_sw'] === StrategyApp::CRASH_SWITCH_ON && $crashType === 'all') {
            $data['crash_list'] = json_encode([]);
        } else {
//            $data['crash_list'] = str_replace(["\r\n", "\n"], ",", $data['crash_list']);
            $data['crash_list'] = json_encode($data['crash_list']);
        }

        StrategyApp::query()->where('id', $id)->update($data);

        if ($data) {
            //触发更新app的事件
            Event::fire(new UpdateApp($appId));
        }

        return $this->jsonResponse();

        //处理添加队列
//        $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
//            'credentials' => Grpc\ChannelCredentials::createInsecure()
//        ]);
//        $args = new SqsQueueParam();
//        $args->setType(3);
//        $args->setId(intval($appId));
//        $res = $client->PlacementSqsQueue($args)->wait();
//        if ($res[1]->code !== 0) {
//            Log::info("update placement queue failed:" . $res[1]->details);
//        } else {
//            Log::info("update placement queue succ");
//        }
    }
}
