<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\SdkChannel;
use App\Models\MySql\StrategyPlugin;
use App\Models\MySql\StrategySDKPlugin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class StrategyPluginController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $paginator = StrategyPlugin::query()
            ->from(StrategyPlugin::TABLE . ' as t1')
            ->leftJoin(NetworkFirm::TABLE . ' as t2', 't2.id', '=', 't1.nw_firm_id')
            ->leftJoin(SdkChannel::TABLE . ' as t3', 't1.channel_id', '=', 't3.id')
            ->select([
                't1.id', 't1.nw_firm_id', 't2.name as nw_firm_name', 't1.platform',
                't1.package_upload_address_list as upload_url',
                't1.pkg_address_timeout_min', 't1.pkg_address_timeout_max', 't1.status',
                't1.channel_id','t3.remark as channel_remark', 't3.name as channel_name',
            ])
            ->orderByDesc('id')
            ->paginate($request->query('page_size', 20), ['*'], 'page_no', $request->query('page_no'));

        return $this->jsonResponse($this->parseResByPaginator($paginator));
    }

    /**
     * 白名单信息
     *
     * @return JsonResponse
     */
    public function whiteList(): JsonResponse
    {
        $whiteList = StrategySDKPlugin::query()->where('type', '1')->first();

        if ($whiteList == null) {
            return $this->jsonResponse([], 10003);
        }

        $data = [
            'android_id_list' => json_decode($whiteList['android_ids'], true),
            'idfa_list'       => json_decode($whiteList['idfas'], true),
            'idfv_list'       => json_decode($whiteList['idfvs'], true),
            'package_list'    => json_decode($whiteList['packages'], true),
        ];
        return $this->jsonResponse($data);
    }

    /**
     * 更新白名单
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateWhiteList(Request $request): JsonResponse
    {
        $rules = [
            'android_id_list'   => ['present', 'array'],
            'idfa_list'         => ['present', 'array'],
            'idfv_list'         => ['present', 'array'],
            'package_list'      => ['present', 'array'],
            'android_id_list.*' => ['distinct'],
            'idfa_list.*'       => ['distinct'],
            'idfv_list.*'       => ['distinct'],
            'package_list.*'    => ['distinct'],
        ];
        $this->validate($request, $rules);

        $whiteList = StrategySDKPlugin::query()->where('type', 1)->firstOrFail();

        $whiteList->update([
            'android_ids' => json_encode($request->get('android_id_list')),
            'idfas'       => json_encode($request->get('idfa_list')),
            'idfvs'       => json_encode($request->get('idfv_list')),
            'packages'    => json_encode($request->get('package_list')),
            'admin_id'    => auth('api')->id(),
            'update_time' => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonResponse();
    }

    /**
     * 创建时的 notice_list 默认值由后端提供
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
//        $noticeList = StrategyPlugin::query()->where('nw_firm_id', 0)->value('notice_list');
//        return $this->jsonResponse([
//            'notice_list' => $noticeList ?? '',
//        ]);
        
        return $this->jsonResponse([
            'notice_list' => ''
        ]);
    }

    public function show($id): JsonResponse
    {
        $strategy = StrategyPlugin::query()
            ->select([
                'channel_id','package_match_list', 'package_upload_address_list as upload_url', 'report_tk_params',
                'pkg_address_timeout_min', 'pkg_address_timeout_max', 'platform', 'notice_list', 'nw_firm_id'
            ])->where('id', $id)->firstOrFail()->toArray();

        return $this->jsonResponse($strategy);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'channel_id'              => ['required', 'integer'],
            'notice_list'             => ['present', 'json'],
            'platform'                => ['required', Rule::in(array_keys(App::getPlatformMap()))],
            'nw_firm_id'              => ['required', 'integer'],
            'package_match_list'      => ['present', 'json'],
            'upload_url'              => ['present', 'string'],
            'report_tk_params'        => ['present', 'json'],
            'pkg_address_timeout_min' => ['required', 'integer'],
            'pkg_address_timeout_max' => ['required', 'integer'],
        ];
        
        // nw_firm_id 为0表示通用平台
        if($request->get('nw_firm_id') > 0){
            $rules['nw_firm_id'] = ['required', 'exists:network_firm,id'];
            unset($rules['notice_list']);
            $request->offsetSet('notice_list', '');  //通用平台才有notice_list
        }
        
        // channel_id 为0表示通用版本
        if($request->get('channel_id') > 0){
            $rules['channel_id'] = ['required', 'integer', Rule::exists(SdkChannel::TABLE,'id')];
            unset($rules['platform']);
            $request->offsetSet('platform', SdkChannel::query()->where('id', $request->get('channel_id'))->value('platform'));
        }
        
        $this->validate($request, $rules);

        $channelId  = $request->get('channel_id');
        $nwFirmId   = $request->get('nw_firm_id');
        $platform   = $request->get('platform');
        $noticeList = $request->get('notice_list');
    
        // 添加前先判断该渠道版本&系统平台下是否已经有通用广告平台
        if($nwFirmId && !$this->hasRecord($channelId, $platform, 0)) {
            return $this->jsonResponse([], 10000, "该版本&系统平台还没有配置通用广告平台");
        }
        
        // 检查渠道版本&系统平台&广告平台是否重复
        if ($this->hasRecord($channelId, $platform, $nwFirmId)) {
            return $this->jsonResponse([], 10000, "该广告平台&系统平台&版本的平台策略已配置");
        }

        StrategyPlugin::query()->insert([
            'channel_id'                  => $channelId,
            'notice_list'                 => $noticeList,
            'platform'                    => $platform,
            'nw_firm_id'                  => $nwFirmId,
            'package_match_list'          => $request->get('package_match_list'),
            'package_upload_address_list' => $request->get('upload_url'),
            'pkg_address_timeout_min'     => $request->get('pkg_address_timeout_min'),
            'pkg_address_timeout_max'     => $request->get('pkg_address_timeout_max'),
            'report_tk_params'            => $request->get('report_tk_params'),
            'create_time'                 => date('Y-m-d H:i:s'),
            'update_time'                 => date('Y-m-d H:i:s'),
            'status'                      => StrategyPlugin::STATUS_RUNNING,
        ]);

        return $this->jsonResponse([], 1);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'notice_list'             => ['nullable', 'json'],
            'package_match_list'      => ['nullable', 'json'],
            'upload_url'              => ['nullable', 'string'],
            'report_tk_params'        => ['nullable', 'json'],
            'pkg_address_timeout_min' => ['nullable', 'integer'],
            'pkg_address_timeout_max' => ['nullable', 'integer'],
            'status'                  => ['nullable', Rule::in(array_keys(StrategyPlugin::getStatusMap()))]
        ];
        $this->validate($request, $rules);

        $strategy = StrategyPlugin::query()->where('id', $id)->firstOrFail();
        // nw_firm_id 为0表示通用平台
        if($strategy['nw_firm_id'] > 0){
            $request->offsetSet('notice_list', '');  //通用平台才有notice_list
        }
        
        $strategy->update([
            'notice_list'                 => $request->has('notice_list')             ? $request->get('notice_list')             : $strategy['notice_list'],
            'package_match_list'          => $request->has('package_match_list')      ? $request->get('package_match_list')      : $strategy['package_match_list'],
            'package_upload_address_list' => $request->has('upload_url')              ? $request->get('upload_url')              : $strategy['package_upload_address_list'],
            'report_tk_params'            => $request->has('report_tk_params')        ? $request->get('report_tk_params')        : $strategy['report_tk_params'],
            'pkg_address_timeout_min'     => $request->has('pkg_address_timeout_min') ? $request->get('pkg_address_timeout_min') : $strategy['pkg_address_timeout_min'],
            'pkg_address_timeout_max'     => $request->has('pkg_address_timeout_max') ? $request->get('pkg_address_timeout_max') : $strategy['pkg_address_timeout_max'],
            'status'                      => $request->has('status')                  ? $request->get('status')                  : $strategy['status'],
        ]);
        return $this->jsonResponse();
    }
    
    /**
     * 检查渠道版本+系统平台+广告平台是否已存在
     * @param $channelId
     * @param $platform
     * @param $nwFirmId
     * @return bool
     */
    private function hasRecord($channelId, $platform, $nwFirmId)
    {
        $isExist = StrategyPlugin::query()
            ->where('channel_id', $channelId)
            ->where('platform', $platform)
            ->where('nw_firm_id', $nwFirmId)
            ->exists();
        return $isExist;
    }
}
