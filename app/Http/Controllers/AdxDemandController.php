<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\MySql\Users;
use App\Models\MySql\AdxDemand;

use App\Helpers\TimeConversion;

class AdxDemandController extends ApiController
{
    /**
     * Demand 列表
     * http://yapi.toponad.com/project/18/interface/api/653
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $id       = $request->input('id');
        $name     = $request->input('name');
        $type     = $request->input('type');
        $remark   = $request->input('remark');
        $status   = $request->input('status');
        $pageSize = $request->get('page_size', 10);
        $pageNo   = $request->get('page_no', 1);

        $query = AdxDemand::query();
        if($id > 0){
            $query->where('id', $id);
        }
        if(!empty($name)){
            $query->where('name', 'like', "%{$name}%");
        }
        if($type > 0){
            $query->where('type', $type);
        }
        if(!empty($remark)){
            $query->where('remark', 'like', "%{$remark}%");
        }
        if($status > 0){
            $query->where('status', $status);
        }

        $selectList = ['*'];
        $paginator  = $query->orderBy('id', 'desc')
            ->paginate($pageSize, $selectList, 'page_no', $pageNo);
        $data       = $this->parseResByPaginator($paginator);

        foreach($data['list'] as &$val){
            $val['admin_name']  = (new Users())->getName($val['admin_id']);
            $val['create_time'] = TimeConversion::dateTimeToMsTimestamp($val['create_time']);
            $val['update_time'] = TimeConversion::dateTimeToMsTimestamp($val['update_time']);
        }

        return $this->jsonResponse($data);
    }

    /**
     * Demand 新增
     * http://yapi.toponad.com/project/18/interface/api/671
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name'             => ['required', 'string'],
            'request_url'      => ['required', 'string'],
            'type'             => ['required', 'integer', Rule::in(array_keys(AdxDemand::getTypeMap()))],
            'billing_type'     => ['required', 'integer', Rule::in(array_keys(AdxDemand::getBillingTypeMap()))],
            'billing_currency' => ['required', 'string',  Rule::in(array_keys(AdxDemand::getBillingCurrencyMap()))],
            'billing_basis'    => ['required', 'integer', Rule::in(array_keys(AdxDemand::getBillingBasisMap()))],
            'remark'           => ['string'],
        ];
        Validator::make($request->all(), $rules)->validate();

        $adxDemand = $request->only([
            'name', 'request_url', 'type', 'billing_type', 'billing_currency', 'billing_basis', 'remark'
        ]);
        $adxDemand['status']   = AdxDemand::STATUS_DOCKING;
        $adxDemand['admin_id'] = Auth::id();
        AdxDemand::query()->create($adxDemand);

        return $this->jsonResponse();
    }

    /**
     * Demand 单条记录
     * http://yapi.toponad.com/project/18/interface/api/662
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $data = AdxDemand::query()
            ->where('id', $id)
            ->first();

        $data->create_time = TimeConversion::dateTimeToMsTimestamp($data->create_time);
        $data->update_time = TimeConversion::dateTimeToMsTimestamp($data->update_time);

        return $this->jsonResponse($data);
    }

    /**
     * Demand 更新
     * http://yapi.toponad.com/project/18/interface/api/680
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $adxDemand = AdxDemand::query()->find($id);

        if ($adxDemand == null || !$adxDemand instanceof AdxDemand) {
            return $this->jsonResponse(['id' => 'ADX demand not found'], 17000);
        }

        $rules = [
            'name'             => ['required', 'string'],
            'request_url'      => ['required', 'string'],
            'type'             => ['required', 'integer', Rule::in(array_keys(AdxDemand::getTypeMap()))],
            'billing_type'     => ['required', 'integer', Rule::in(array_keys(AdxDemand::getBillingTypeMap()))],
            'billing_currency' => ['required', 'string',  Rule::in(array_keys(AdxDemand::getBillingCurrencyMap()))],
            'billing_basis'    => ['required', 'integer', Rule::in(array_keys(AdxDemand::getBillingBasisMap()))],
            'remark'           => ['string'],
            'status'           => ['required', 'integer', Rule::in(array_keys(AdxDemand::getStatusMap()))],
        ];
        Validator::make($request->all(), $rules)->validate();


        $adxDemand->fill($request->only([
            'name', 'request_url', 'type', 'billing_type', 'billing_currency', 'billing_basis', 'remark', 'status'
        ]));
        $adxDemand->update_time = date('Y-m-d H:i:s');
        $adxDemand->admin_id    = Auth::id();

        $adxDemand->update();

        return $this->jsonResponse();
    }

    /**
     * Demand 启用/暂停
     * http://yapi.toponad.com/project/18/interface/api/860
     *
     * @param  Request $request
     * @param  int     $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function status(Request $request, $id){
        $rules = [
            'status' => ['required', 'integer', Rule::in(array_keys(AdxDemand::getStatusMap()))],
        ];
        Validator::make($request->all(), $rules)->validate();

        $adxDemand = AdxDemand::query()->find($id);

        if ($adxDemand == null || !$adxDemand instanceof AdxDemand) {
            return $this->jsonResponse(['id' => 'ADX demand not found'], 17000);
        }

        $adxDemand->fill($request->only([
            'status'
        ]));
        $adxDemand->update_time = date('Y-m-d H:i:s');
        $adxDemand->admin_id    = Auth::id();

        $adxDemand->update();

        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Demand 元数据
     * http://yapi.toponad.com/project/18/interface/api/851
     *
     * @return JsonResponse
     */
    public function meta(): JsonResponse {
        $data = AdxDemand::query()
            ->select('id as value', 'name as label')
            ->where('status', AdxDemand::STATUS_ACTIVE)
            ->get();

        return $this->jsonResponse($data);
    }
}
