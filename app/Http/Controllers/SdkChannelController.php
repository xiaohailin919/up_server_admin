<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\MySql\SdkChannel;
use App\Models\MySql\Users;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class SdkChannelController extends ApiController
{
    /**
     * sdk定制渠道号列表
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
        $request->filled('channel_id')   && $where[] = ['t1.id', '=', $channelId];
        $request->filled('channel_name') && $where[] = ['t1.name', 'like', "%{$channelName}%"];
        $request->input('platform')      && $where[] = ['t1.platform', '=', $platform];
        
        list($t1, $t2) = [SdkChannel::TABLE, Users::TABLE];
        $paginator = SdkChannel::query()
            ->from("{$t1} as t1")
            ->leftJoin("{$t2} as t2", 't1.admin_id', '=', 't2.id')
            ->where($where)
            ->orderByDesc('t1.update_time')
            ->orderByDesc('t1.id')
            ->paginate($pageSize, ['t1.*', 't2.name as admin_name'], 'page_no', $pageNo);
        
        return $this->jsonResponse($this->parseResByPaginator($paginator));
    }
    
    /**
     * 全部sdk定制渠道号列表
     *
     * @return \Illuminate\Http\Response
     */
    public function metaAll(Request $request)
    {
        list($t1, $t2) = [SdkChannel::TABLE, Users::TABLE];
        $result = SdkChannel::query()
            ->from("{$t1} as t1")
            ->leftJoin("{$t2} as t2", "t1.admin_id", '=', "t2.id")
            ->orderByDesc('t1.update_time')
            ->orderByDesc('t1.id')
            ->select(['t1.id as channel_id', 't1.name as channel_name', 't1.platform','t1.status'])
            ->get()
            ->toArray();
        
        foreach($result as &$row){
            $row['disabled'] = $row['status'] === SdkChannel::STATUS_ACTIVE ? false : true;
        }
        
        return $this->jsonResponse($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'      => ['required'],
            'platform'  => ['required', Rule::in(array_keys(SdkChannel::getPlatformMap()))],
            'remark'    => ['nullable'],
            //'status'    => ['required', Rule::in(array_keys(SdkChannel::getStatusMap()))]
        ];
        $this->validate($request, $rules);
        
        $data = [];
        $data['name']        = $request->input('name');
        $data['platform']    = $request->input('platform');
        $data['remark']      = $request->input('remark', '');
        $data['status']      = SdkChannel::STATUS_ACTIVE; // 都是启用，前端隐藏"状态"
        
        // 补充字段
        $data['admin_id']    = Auth()->id();
        $data['create_time'] = Utils::getDateTime();
        $data['update_time'] = Utils::getDateTime();
        
        SdkChannel::query()->insert($data);
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
        if(!SdkChannel::query()->where('id', $id)->exists()){
            return $this->jsonResponse([],10003);
        }
        
        list($t1, $t2) = [SdkChannel::TABLE, Users::TABLE];
        $result = SdkChannel::query()
            ->from("{$t1} as t1")
            ->leftJoin("{$t2} as t2", 't1.admin_id', '=', 't2.id')
            ->select(['t1.*', 't2.name as admin_name'])
            ->where('t1.id',$id)
            ->first()
            ->toArray();
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
        if(!SdkChannel::query()->where('id', $id)->exists()){
            return $this->jsonResponse([],10003);
        }
        
        $rules = [
            'name'      => ['required'],
            'remark'    => ['nullable'],
            //'status'    => ['required', Rule::in(array_keys(SdkChannel::getStatusMap()))]
        ];
        $this->validate($request, $rules);
    
        $data = [];
        $data['name']        = $request->input('name');
        $data['remark']      = $request->input('remark', '');
        // $data['status']      = $request->input('status');
        
        // 补充字段
        $data['admin_id']    = Auth()->id();
        $data['update_time'] = Utils::getDateTime();
    
        SdkChannel::query()->where('id',$id)->update($data);
        return $this->jsonResponse();
    }
    
}
