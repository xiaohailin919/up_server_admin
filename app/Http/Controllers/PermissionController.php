<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission as PermissionModel;
use App\Services\Permission as PermissionService;
use Spatie\Permission\Models\Role;

class PermissionController extends ApiController {

    const INTERMEDIATE_TABLE = 'role_has_permissions';

    /**
     * 列表
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $permissions = PermissionModel::all();

        /* 查出所有有对应权限的角色 */
        $roleHasPermissions = DB::query()->from(self::INTERMEDIATE_TABLE . ' as t1')
            ->leftJoin(Role::TABLE . ' as t2', 't2.id', '=', 't1.role_id')
            ->select(['t1.role_id', 't1.permission_id', 't2.name as role_name'])
            ->get()->toArray();

        $map = [];

        foreach ($roleHasPermissions as $roleHasPermission) {
            $map[$roleHasPermission['permission_id']][] = [
                'label' => $roleHasPermission['role_name'],
                'value' => $roleHasPermission['role_id']
            ];
        }

        foreach ($permissions as $idx => $permission) {
            $permissions[$idx]['role_list'] = $map[$permission['id']] ?? [];
        }

        return $this->jsonResponse($permissions);
    }

    /**
     * 元数据
     *
     * @return JsonResponse
     */
    public function meta(): JsonResponse
    {
        $permissions = PermissionModel::all(['id as value', 'name as label'])->toArray();

        return $this->jsonResponse($permissions);
    }

    /**
     * 获取按分类分的 mata 数据
     *
     * @return JsonResponse
     */
    public function metaRecursive(): JsonResponse
    {
        $permissions = PermissionModel::all(['id', 'name'])->toArray();
        $permissionNameIdMap = array_column($permissions, 'id', 'name');
        $res = [];

        /* Permission 文件中配置的所有权限 */
        $writtenPermissions = PermissionService::getAllPermissions();
        $added = [];

        foreach ($writtenPermissions as $category => $categoryPermissions) {
            $tmp = [
                'label' => $category,
                'value' => $category,
                'children' => [],
            ];
            /* 遍历该分类下的所有权限，如果未添加，就加入返回结果中 */
            foreach ($categoryPermissions['list'] as $permission) {
                if (array_key_exists($permission['permission_name'], $permissionNameIdMap) && !in_array($permission['permission_name'], $added, true)) {
                    $tmp['children'][] = [
                        'label' => $permission['permission_name'],
                        'value' => $permissionNameIdMap[$permission['permission_name']],
                    ];
                    $added[] = $permission['permission_name'];
                }
            }
            $res[] = $tmp;
        }
        return $this->jsonResponse($res);
    }

    /**
     * 更新权限
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $rules = [
            'role_id_list'   => ['present', 'array'],
            'role_id_list.*' => ['exists:roles,id'],
        ];
        $this->validate($request, $rules);

        $permission = PermissionModel::query()->where('id', $id)->firstOrFail();

        assert($permission instanceof PermissionModel);

        $permission->roles()->sync($request->get('role_id_list'));

        return $this->jsonResponse();
    }
}
