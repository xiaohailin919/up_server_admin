<?php

namespace App\Http\Controllers;


use App\Helpers\Export;
use App\Models\MySql\AppTerm;
use App\Models\MySql\AppTermRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppTermController extends ApiController
{

    /**
     * 应用标签列表
     * http://192.168.86.20:3000/project/18/interface/api/41
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {

        $this->checkAccessPermission('app-term@index');

        $appTerms = AppTerm::query()
            ->leftJoin('users', 'users.id', '=', 'app_term.admin_id')
            ->orderBy('app_term.update_time')
            ->get(['app_term.*', 'users.name as admin_name'])
            ->toArray();
        $appTypes        = array_values(array_where($appTerms, static function ($value) {return $value['type'] === AppTerm::TYPE_APP_TYPE;}));
        $appFirstLabels  = array_values(array_where($appTerms, static function ($value) {return $value['type'] === AppTerm::TYPE_LABEL_PARENT;}));
        $res = [
            'type' => ['list' => [], 'admin_id' => 0, 'admin_name' => '', 'update_time' => 0],
            'label' => ['list' => []],
        ];
        $updateTime = 0;
        foreach ($appTypes as $appType) {
            /* 删除也是修改，所有无论是增加还是删除，时间和操作人员都应该根据记录进行更新 */
            if ($updateTime < $appType['update_time']) {
                $res['type']['update_time'] = $updateTime = $appType['update_time'];
                $res['type']['admin_id'] = $appType['admin_id'];
                $res['type']['admin_name'] = $appType['admin_name'];
            }
            if ($appType['status'] == AppTerm::STATUS_STOP) {
                continue;
            }
            $res['type']['list'][] = ['id' => $appType['id'], 'name' => $appType['name'],];
        }

        foreach ($appFirstLabels as $appFirstLabel) {
            /* 一级标签一旦关闭，肯定跳过 */
            if ($appFirstLabel['status'] == AppTerm::STATUS_STOP) {
                continue;
            }
            $item = [
                'id' => $appFirstLabel['id'],
                'name' => $appFirstLabel['name'],
                'children' => [],
                'admin_id' => $appFirstLabel['admin_id'],
                'admin_name' => $appFirstLabel['admin_name'],
                'update_time' => $appFirstLabel['update_time'],
            ];
            $secondLabels = array_values(array_where($appTerms, static function($value) use($appFirstLabel) {
                return $value['type'] === AppTerm::TYPE_LABEL_CHILD && $value['parent_id'] == $appFirstLabel['id'];
            }));
            $updateTime = 0;
            foreach ($secondLabels as $secondLabel) {
                /* 删除也是修改，所有无论是增加还是删除，时间和操作人员都应该根据记录进行更新 */
                if ($updateTime < $secondLabel['update_time']) {
                    $item['update_time'] = $updateTime = $secondLabel['update_time'];
                    $item['admin_id']  = $secondLabel['admin_id'];
                    $item['admin_name'] = $secondLabel['admin_name'];
                }
                if ($secondLabel['status'] == AppTerm::STATUS_STOP) {
                    continue;
                }
                $item['children'][] = ['id' => $secondLabel['id'], 'name' => $secondLabel['name'], 'children' => []];
            }
            $res['label']['list'][] = $item;
        }

        return $this->jsonResponse($res);
    }

    /**
     * 导出报表
     */
    public function export() {

        $this->checkAccessPermission('app-term@index');

        $records = AppTerm::query()
            ->leftJoin('app_term as t2', 't2.id', '=', 'app_term.parent_id')
            ->leftJoin('users as t3', 't3.id', '=', 'app_term.admin_id')
            ->where('app_term.status', AppTerm::STATUS_ACTIVE)
            ->where('app_term.type', '!=', AppTerm::TYPE_LABEL_PARENT)
            ->get(['app_term.*', 't2.name as parent_name', 't3.name as admin_name'])->toArray();

//        return $this->jsonResponse($records);

        $exportHeader = [
            __('common.app_term.term_type'),
            __('common.app_term.name'),
            __('common.admin_name'),
            __('common.create_time'),
            __('common.update_time'),
        ];

        $exportData = [];
        foreach ($records as $record) {
            $datum = [];
            $datum[] = empty($record['parent_name']) ? __('common.app_term.type') : $record['parent_name'];
            $datum[] = $record['name'];
            $datum[] = $record['admin_name'];
            $datum[] = date('Y-m-d H:i:s', $record['create_time'] / 1000);
            $datum[] = date('Y-m-d H:i:s', $record['update_time'] / 1000);
            $exportData[] = $datum;
        }

        Export::exportAsCsv($exportData, $exportHeader, 'app_labels_' . date('Ymd_His') . '.csv');
    }

    /**
     * 创建标签
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {

        $this->checkAccessPermission('app-term@store');

        $rules = [
            'name'      => 'required|string',
            'type'      => 'required|integer|in:1,2,3',
            'parent_id' => 'required|integer',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 10000);
        }
        if ($request['parent_id'] != AppTerm::PARENT_NONE && ($request['type'] == AppTerm::TYPE_APP_TYPE || $request['type'] == AppTerm::TYPE_LABEL_PARENT)) {
            return $this->jsonResponse(['parent_id' => ['广告类型或一级标签不允许有父标签。']]);
        }
        if ($request['type'] == AppTerm::TYPE_LABEL_CHILD &&
            !AppTerm::query()
                ->where('status', AppTerm::STATUS_ACTIVE)
                ->where('id', $request['parent_id'])
                ->where('type', AppTerm::TYPE_LABEL_PARENT)->exists()) {
            return $this->jsonResponse(['parent_id' => ['二级标签必须指定一级标签作为父标签。']]);
        }

        $appTerm = AppTerm::query()
            ->where('type', $request['type'])
            ->where('name', $request['name'])
            ->where('parent_id', $request['parent_id'])
            ->first();

        if ($appTerm !== null) {
            if ($appTerm['status'] != AppTerm::STATUS_ACTIVE) {
                $appTerm->update(['status' => AppTerm::STATUS_ACTIVE, 'admin_id' => auth()->id()]);
                return $this->jsonResponse([],1);
            }
            return $request['type'] == AppTerm::TYPE_APP_TYPE ? $this->jsonResponse([], 11005) : $this->jsonResponse([], 11006);
        }

        AppTerm::query()->create([
            'name'      => $request['name'],
            'type'      => $request['type'],
            'parent_id' => $request['parent_id'],
            'status'    => AppTerm::STATUS_ACTIVE,
            'admin_id'  => auth()->id(),
        ]);
        return $this->jsonResponse([],1);
    }

    /**
     * 删除标签
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse {

        $this->checkAccessPermission('app-term@destroy');

        $appTerm = AppTerm::query()->find($id);

        if ($appTerm != null && $appTerm['status'] != AppTerm::STATUS_STOP) {
            $appTerm->update(['status' => AppTerm::STATUS_STOP, 'admin_id' => auth()->id()]);
            /* 解绑与该标签有关的 APP */
            AppTermRelationship::query()->where('term_id', $id)->delete();
        }
        return $this->jsonResponse([],2);
    }
}
