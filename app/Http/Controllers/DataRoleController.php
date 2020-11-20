<?php

namespace App\Http\Controllers;

use App\Models\MySql\DataRole;
use App\Services\DataRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DataRoleController extends ApiController
{

    /**
     * 数据权限角色 - 列表
     * 支持搜索：name, type, admin_id
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $query = DataRole::query()
            ->leftJoin('users', 'users.id', '=', 'data_role.admin_id')
            ->select(['data_role.*'])
            ->selectRaw("IFNULL(users.name, '-') as admin_name");

        if ($request->has('name')) {
            $query->where('data_role.name', 'like', '%' . $request->query('name') . '%');
        }
        if (array_key_exists($request->query('type', -1), DataRole::getTypeMap())) {
            $query->where('data_role.type', '=', $request->query('type'));
        }
        if ($request->query('admin_id', -1) > -1) {
            $query->where('data_role.admin_id', '=', $request->query('admin_id'));
        }
        if ($request->has('order_by_field_list')) {
            $orderByFieldList = $request->query('order_by_field_list');
            $orderByDirectionList = $request->query('order_by_direction_list');
            for ($i = 0, $iMax = count($orderByFieldList); $i < $iMax; $i++) {
                $query->orderBy($orderByFieldList[$i], $orderByDirectionList[$i]);
            }
        } else {
            $query->orderBy('data_role.type');
        }

        $paginator = $query->paginate($request->query('page_size', 25), ['*'], 'page_no', $request->query('page_no', 1));
        $res = $this->parseResByPaginator($paginator);
        $res['list'] = DataRoleService::convertToViewModel($res['list']);

        return $this->jsonResponse($res);
    }

    /**
     * 数据权限角色 - 创建
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $rules = [
            'name_list' => ['required', 'array'],
            'type' => ['required', 'integer', Rule::in(array_keys(DataRole::getTypeMap()))],
        ];
        $this->validate($request, $rules);

        $existNameList = array_column(DataRole::query()->get(['name'])->toArray(), 'name');
        $newRecords = [];

        $type = $request->input('type');
        $nameList = $request->input('name_list');

        foreach ($nameList as $item) {
            if (in_array($item, $existNameList, false)) {
                continue;
            }
            $newRecords[] = [
                'name' => $item,
                'type' => $type,
                'admin_id' => auth('api')->id(),
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
        }

        $res = DataRole::query()->insert($newRecords);

        return $res ? $this->jsonResponse([], 1) : $this->jsonResponse([], 9995);
    }

    /**
     * 数据权限角色 - 获取
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $dataRole = DataRole::query()
            ->leftJoin('users', 'users.id', '=', 'data_role.admin_id')
            ->select(['data_role.*', 'users.name as admin_name'])
            ->selectRaw("IFNULL(users.name, '-') as admin_name")
            ->find($id);

        if ($dataRole === null || !$dataRole instanceof DataRole) {
            return $this->jsonResponse([], 10000);
        }
        $dataRole = DataRoleService::convertToViewModel([$dataRole->toArray()]);

        return $this->jsonResponse($dataRole[0]);
    }

    /**
     * 数据权限角色 - 更新
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $existsNames = array_column(DataRole::query()->where('id', '!=', $id)->get(['name'])->toArray(), 'name');
        $rules = [
            'name' => ['required', 'string', Rule::notIn($existsNames)],
            'type' => ['required', 'integer', Rule::in(array_keys(DataRole::getTypeMap()))],
        ];
        $this->validate($request, $rules);

        $dataRole = DataRole::query()->find($id);
        if ($dataRole === null || !$dataRole instanceof DataRole) {
            return $this->jsonResponse([], 10000);
        }

        $res = $dataRole->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'admin_id' => auth('api')->id(),
            'update_time' => date('Y-m-d H:i:s'),
        ]);

        return $res === false ? $this->jsonResponse([], 9995) : $this->jsonResponse(['id' => $id]);
    }

    /**
     * 数据权限角色 - 删除
     * 不实现
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        return $this->jsonResponse(['id' => $id], 9996);
    }
}
