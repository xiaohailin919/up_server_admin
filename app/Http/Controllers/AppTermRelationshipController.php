<?php

namespace App\Http\Controllers;

use App\Helpers\ArrayUtil;
use App\Models\MySql\AppTerm;
use App\Models\MySql\AppTermRelationship;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppTermRelationshipController extends ApiController {

    /**
     * 应用 - 单个 App 更新产品类型或标签列表
     * http://192.168.86.20:3000/project/18/interface/api/149
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'app_id'    => 'required|integer|exists:app,id',
            'type'      => 'required|integer|in:' . AppTerm::TYPE_APP_TYPE . ',' . AppTerm::TYPE_LABEL_CHILD,
            'id_list'   => 'nullable|array',
            'id_list.*' => ['nullable', 'integer', Rule::in($typeIdListMap[$request->get('type', 2)])]
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }

        /* 为条记录更新绑定信息，同时验证 term_id 列表中的数据是否属于该种类型 */
        $appTermTypeIdListMap = AppTerm::getTypeIdListMap();
        DB::beginTransaction();
        /* 把原有该 AppId 该类型下所有绑定关系都移除 */
        AppTermRelationship::query()->where('app_id', $request['app_id'])
            ->whereIn('term_id', $appTermTypeIdListMap[$request['type']])->delete();
        foreach ($request['id_list'] as $j => $id) {
            if (in_array($id, $appTermTypeIdListMap[$request['type']], false)) {
                AppTermRelationship::query()->create(['app_id' => $request['app_id'], 'term_id' => $id]);
            } else {
                DB::rollback();
                return $this->jsonResponse(['id_list.' . $j => 'The term_id is not belong the specific type.'], 11003);
            }
        }
        DB::commit();
        return $this->jsonResponse([], 1);
    }

    /**
     * 多个 APP 批量绑定应用类型
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function multiStoreType(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'id_list'       => 'required',
            'id_list.*'     => ['required', 'distinct', Rule::in($typeIdListMap[AppTerm::TYPE_APP_TYPE])],
            'app_id_list'   => 'required',
            'app_id_list.*' => 'required|exists:app,id',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }
        DB::beginTransaction();
        try {
            /* 直接解绑所有 App 与应用标签 */
            AppTermRelationship::query()->whereIn('app_id', $request['app_id_list'])
                ->whereIn('term_id', $typeIdListMap[AppTerm::TYPE_APP_TYPE])
                ->delete();
            /* 绑定所有 App 与新的应用标签，使用数组的第一项。 */
            $newRecords = [];
            foreach ($request['app_id_list'] as $appId) {
                $newRecords[] = [
                    'app_id'      => $appId,
                    'term_id'     => $request['id_list'][0],
                    'create_time' => date('Y-m-d H:i:s'),
                ];
            }
            AppTermRelationship::query()->insert($newRecords);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([], 9998, 'DB operation revoked');
        }
        return $this->jsonResponse([],1);
    }

    /**
     * 批量绑定多个 App 和多个应用标签
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function multiStoreLabel(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'id_list'       => 'required',
            'id_list.*'     => ['required', 'distinct', Rule::in($typeIdListMap[AppTerm::TYPE_LABEL_CHILD])],
            'app_id_list'   => 'required',
            'app_id_list.*' => 'required|exists:app,id',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }

        /* 获取这些 App 中已绑定的标签的父级标签列表 */
        $parentLabelIdListByApp = AppTermRelationship::query()
            ->from('app_term_relationship as t1')
            ->leftJoin('app_term as t2', 't1.term_id', '=', 't2.id')
            ->whereIn('t1.app_id', $request['app_id_list'])
            ->where('t2.type', AppTerm::TYPE_LABEL_CHILD)
            ->groupBy(['t2.parent_id'])
            ->get(['t2.parent_id'])->toArray();
        $parentLabelIdListByApp = array_column($parentLabelIdListByApp, 'parent_id');
        if (count($parentLabelIdListByApp) > 1) {
            return $this->jsonResponse([], 11007);
        }

        /* 获取这些 Labels 的父级标签 */
        $parentLabelIdListByLabels = AppTerm::query()
            ->whereIn('id', $request['id_list'])
            ->groupBy(['parent_id'])->get(['parent_id'])->toArray();
        $parentLabelIdListByLabels = array_column($parentLabelIdListByLabels, 'parent_id');
        if (count($parentLabelIdListByLabels) > 1) {
            return $this->jsonResponse([], 11008);
        }
        if (!empty($parentLabelIdListByApp[0]) && ($parentLabelIdListByApp[0] != $parentLabelIdListByLabels[0])) {
            return $this->jsonResponse([], 11004);
        }

        /* 查出每个 App 原已经绑定的标签 */
        $appLabels = AppTermRelationship::query()
            ->select('app_id')->selectRaw('group_concat(term_id) as term_ids')
            ->whereIn('app_id', $request['app_id_list'])->groupBy(['app_id'])->get()->toArray();
        $appLabels = array_column($appLabels, 'term_ids', 'app_id');
        $appIdLabelListMap = [];
        foreach ($appLabels as $appId => $appTerm) {
            $appIdLabelListMap[$appId] = ArrayUtil::explodeString($appTerm, ',');
        }

        /* 对于原先没有的绑定关系进行绑定 */
        $newRecords = [];
        foreach ($request['app_id_list'] as $appId) {
            foreach ($request['id_list'] as $labelId) {
                /* 如果原先应用已经绑定了该标签，直接跳过 */
                if (array_key_exists($appId, $appIdLabelListMap) && in_array($labelId, $appIdLabelListMap[$appId], false)) {
                    continue;
                }
                $newRecords[] = [
                    'app_id' => $appId,
                    'term_id' => $labelId,
                    'create_time' => date('Y-m-d H:i:s')
                ];
            }
        }
        DB::beginTransaction();
        try {
            AppTermRelationship::query()->insert($newRecords);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([], 9995);
        }
        return $this->jsonResponse([],1);
    }

    /**
     * 批量换绑标签
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function multiReplaceLabel(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'app_id_list'   => 'required|array',
            'app_id_list.*' => 'exists:app,id',
            'id_list'       => 'required|array',
            'id_list.*'     => ['required', 'distinct', Rule::in($typeIdListMap[AppTerm::TYPE_LABEL_CHILD])]
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }

        DB::beginTransaction();
        try {
            /* 把所有应用原本绑定的标签全部去除 */
            AppTermRelationship::query()->whereIn('app_id', $request['app_id_list'])
                ->whereIn('term_id', $typeIdListMap[AppTerm::TYPE_LABEL_CHILD])
                ->delete();
            /* 逐个 App 逐个标签进行绑定 */
            $newRecords = [];
            foreach ($request['app_id_list'] as $appId) {
                foreach ($request['id_list'] as $labelId) {
                    $newRecords[] = [
                        'app_id'      => $appId,
                        'term_id'     => $labelId,
                        'create_time' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            AppTermRelationship::query()->insert($newRecords);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            return $this->jsonResponse([], 9995);
        }
        return $this->jsonResponse([],1);
    }

    /**
     * 批量删除多个应用的指定应用类型
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function multiDestroyType(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'id_list'       => 'required|array',
            'id_list.*'     => ['required', 'distinct', Rule::in($typeIdListMap[AppTerm::TYPE_APP_TYPE])],
            'app_id_list'   => 'required|array',
            'app_id_list.*' => 'exists:app,id',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }
        DB::beginTransaction();
        try {
            AppTermRelationship::query()->whereIn('app_id', $request['app_id_list'])
                ->whereIn('term_id', $request['id_list'])
                ->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([], 9995);
        }
        return $this->jsonResponse();
    }

    /**
     * 批量删除多个应用的指定应用标签
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    protected function multiDestroyLabel(Request $request): JsonResponse {

        $this->checkAccessPermission('app_edit');

        $typeIdListMap = AppTerm::getTypeIdListMap();
        $rules = [
            'id_list'       => 'required|array',
            'id_list.*'     => ['required', 'distinct', Rule::in($typeIdListMap[AppTerm::TYPE_LABEL_CHILD])],
            'app_id_list'   => 'required|array',
            'app_id_list.*' => 'exists:app,id',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }
        DB::beginTransaction();
        try {
            foreach ($request['app_id_list'] as $appId) {
                AppTermRelationship::query()->where('app_id', $appId)
                    ->whereIn('term_id', $request['id_list'])
                    ->delete();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([], 9995);
        }
        return $this->jsonResponse();
    }
}
