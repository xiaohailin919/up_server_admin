<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\MySql\SdkChannel;
use App\Models\MySql\SdkInhouseStrategy;
use App\Models\MySql\Users;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class SdkInhouseStrategyController extends ApiController
{
    /**
     * sdk定制渠道策略列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize       = $request->input('page_size', 20);
        $pageNo         = $request->input('page_no', 1);
        
        $channelId      = $request->input('channel_id', '');
        $channelName    = $request->input('channel_name', '');
        $platform       = $request->input('platform', 0);
        
        $where = [];
        $request->filled('channel_id')   && $where[] = ['t1.channel_id', '=', $channelId];
        $request->filled('channel_name') && $where[] = ['t3.name', 'like', "%{$channelName}%"];
        $request->input('platform')      && $where[] = ['t1.platform', '=', $platform];
        
        list($t1, $t2, $t3) = [SdkInhouseStrategy::TABLE, Users::TABLE, SdkChannel::TABLE];
        $paginator = SdkInhouseStrategy::query()
            ->from("{$t1} as t1")
            ->leftJoin("{$t2} as t2", 't1.admin_id', '=', 't2.id')
            ->leftJoin("{$t3} as t3", 't1.channel_id', '=', 't3.id')
            ->where($where)
            ->orderByDesc('t1.update_time')
            ->orderByDesc('t1.id')
            ->paginate($pageSize, ['t1.id', 't1.channel_id', 't1.admin_id','t1.status', 't1.platform', 't2.name as admin_name', 't3.name as channel_name', 't1.create_time', 't1.update_time'], 'page_no', $pageNo);
        
        return $this->jsonResponse($this->parseResByPaginator($paginator));
    }
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->getValidateRules();
        $this->validate($request, $rules);
        
        $data = [];
        $data['channel_id']            = $request->input('channel_id');
        $data['adapter_replacement']   = json_encode($request->input('adapter_replacement', []), JSON_UNESCAPED_UNICODE);
        $data['gdpr_notify_url']       = $request->input('gdpr_notify_url');
        $data['notice_list']           = $request->input('notice_list');
        $data['crash_list']            = json_encode($request->input('crash_list', []), JSON_UNESCAPED_UNICODE);
        $data['req_addr']              = $request->input('req_addr');
        $data['req_tcp_addr']          = $request->input('req_tcp_addr');
        $data['req_tcp_port']          = $request->input('req_tcp_port');
        $data['bid_addr']              = $request->input('bid_addr');
        $data['tk_addr']               = $request->input('tk_addr');
        $data['tk_tcp_addr']           = $request->input('tk_tcp_addr');
        $data['tk_tcp_port']           = $request->input('tk_tcp_port');
        $data['ol_req_addr']           = $request->input('ol_req_addr');
        $data['ol_tcp_addr']           = $request->input('ol_tcp_addr');
        $data['ol_tcp_port']           = $request->input('ol_tcp_port');
        $data['tk_address']            = $request->input('tk_address');
        $data['da_address']            = $request->input('da_address');
        $data['tcp_domain']            = $request->input('tcp_domain');
        $data['tcp_port']              = $request->input('tcp_port');
        $data['m_o_ks_sh']             = $request->input('m_o_ks_sh');
        $data['m_o_ks_do']             = $request->input('m_o_ks_do');
        $data['remark']                = $request->input('remark');
        $data['status']                = $request->input('status');
        
        // 补充字段
        $data['platform']              = SdkChannel::query()->where('id', $data['channel_id'])->value('platform');
        $data['admin_id']              = Auth()->id();
        $data['create_time']           = Utils::getDateTime();
        $data['update_time']           = Utils::getDateTime();
        
        // 过滤null字段
        foreach($data as $key => $val){
            if(is_null($val)){ unset($data[$key]); }
        }
        
        SdkInhouseStrategy::query()->insert($data);
        return $this->jsonResponse();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!SdkInhouseStrategy::query()->where('id', $id)->exists()){
            return $this->jsonResponse([],10003);
        }
        
        list($t1, $t2) = [SdkInhouseStrategy::TABLE, Users::TABLE];
        $result = SdkInhouseStrategy::query()
            ->from("{$t1} as t1")
            ->leftJoin("{$t2} as t2", 't1.admin_id', '=', 't2.id')
            ->select(["t1.*", 't2.name as admin_name'])
            ->where('t1.id', $id)
            ->first()
            ->toArray();
    
        $result['adapter_replacement'] = $result['adapter_replacement'] ? json_decode($result['adapter_replacement']) : [];
        $result['crash_list']          = $result['crash_list'] ? json_decode($result['crash_list']) : [];
        
        return $this->jsonResponse($result);
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!SdkInhouseStrategy::query()->where('id', $id)->exists()){
            return $this->jsonResponse([],10003);
        }
    
        $rules = $this->getValidateRules($id);
        $this->validate($request, $rules);
        
        $data = [];
        $data['adapter_replacement']   = json_encode($request->input('adapter_replacement', []), JSON_UNESCAPED_UNICODE);
        $data['gdpr_notify_url']       = $request->input('gdpr_notify_url');
        $data['notice_list']           = $request->input('notice_list');
        $data['crash_list']            = json_encode($request->input('crash_list', []), JSON_UNESCAPED_UNICODE);
        $data['req_addr']              = $request->input('req_addr');
        $data['req_tcp_addr']          = $request->input('req_tcp_addr');
        $data['req_tcp_port']          = $request->input('req_tcp_port');
        $data['bid_addr']              = $request->input('bid_addr');
        $data['tk_addr']               = $request->input('tk_addr');
        $data['tk_tcp_addr']           = $request->input('tk_tcp_addr');
        $data['tk_tcp_port']           = $request->input('tk_tcp_port');
        $data['ol_req_addr']           = $request->input('ol_req_addr');
        $data['ol_tcp_addr']           = $request->input('ol_tcp_addr');
        $data['ol_tcp_port']           = $request->input('ol_tcp_port');
        $data['tk_address']            = $request->input('tk_address');
        $data['da_address']            = $request->input('da_address');
        $data['tcp_domain']            = $request->input('tcp_domain');
        $data['tcp_port']              = $request->input('tcp_port');
        $data['m_o_ks_sh']             = $request->input('m_o_ks_sh');
        $data['m_o_ks_do']             = $request->input('m_o_ks_do');
        $data['remark']                = $request->input('remark');
        $data['status']                = $request->input('status');
    
        // 补充字段
        $data['admin_id']              = Auth()->id();
        $data['update_time']           = Utils::getDateTime();
    
        // 过滤null字段
        foreach($data as $key => $val){
            if(is_null($val)){ unset($data[$key]); }
        }
        
        SdkInhouseStrategy::query()->where('id',$id)->update($data);
        return $this->jsonResponse();
    }
    
    /**
     * 获取校验规则
     * @param null $id
     * @return array
     */
    private function getValidateRules($id = null)
    {
        $rules = [
            'channel_id'            => ['required', Rule::exists(SdkChannel::TABLE,'id'), Rule::unique(SdkInhouseStrategy::TABLE,'channel_id')],
            'adapter_replacement'   => ['present', 'array'],
            'gdpr_notify_url'       => ['present', 'string'],
            'notice_list'           => ['present', 'json'],
            'crash_list'            => ['present', 'array'],
            'req_addr'              => ['present', 'string'],
            'req_tcp_addr'          => ['present', 'string'],
            'req_tcp_port'          => ['present', 'string'],
            'bid_addr'              => ['present', 'string'],
            'tk_addr'               => ['present', 'string'],
            'tk_tcp_addr'           => ['present', 'string'],
            'tk_tcp_port'           => ['present', 'string'],
            'ol_req_addr'           => ['present', 'string'],
            'ol_tcp_addr'           => ['present', 'string'],
            'ol_tcp_port'           => ['present', 'string'],
            'tk_address'            => ['present', 'string'],
            'da_address'            => ['present', 'string'],
            'tcp_domain'            => ['present', 'string'],
            'tcp_port'              => ['present', 'string'],
            'm_o_ks_sh'             => ['present', 'string'],
            'm_o_ks_do'             => ['present', 'string'],
            'remark'                => ['present', 'string'],
            'status'                => ['required', Rule::in(array_keys(SdkInhouseStrategy::getStatusMap()))]
        ];
        
        // 更新的情况处理
        if($id){
            unset($rules['channel_id']);
        }
        
        return $rules;
    }
}
