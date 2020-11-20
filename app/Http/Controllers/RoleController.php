<?php

namespace App\Http\Controllers;

use App\Rules\NotExists;
use App\Services\Permission as PermissionService;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends ApiController {

    /**
     * 列表
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $roles = Role::all();//Get all roles

        /* Permission 文件中配置的所有权限 */
        $writtenPermissions = PermissionService::getAllPermissions();
        $permissionNameCategoryMap = [];
        foreach ($writtenPermissions as $category => $categoryPermissions) {
            /* 遍历该分类下的所有权限，如果未添加，就加入返回结果中 */
            foreach ($categoryPermissions['list'] as $permission) {
                $permissionNameCategoryMap[$permission['permission_name']] = $category;
            }
        }

        /* 查出所有有对应角色的权限 */
        $roleHasPermissions = DB::query()->from(PermissionController::INTERMEDIATE_TABLE . ' as t1')
            ->leftJoin(Permission::TABLE . ' as t2', 't2.id', '=', 't1.permission_id')
            ->select(['t1.role_id', 't1.permission_id', 't2.name as permission_name'])
            ->get()->toArray();

        $roleIdPermissionListMap = [];

        foreach ($roleHasPermissions as $roleHasPermission) {
            $role = $roleHasPermission['role_id'];
            $name = $roleHasPermission['permission_name'];
            $category = $permissionNameCategoryMap[$name] ?? 'unknown';
            $roleIdPermissionListMap[$role][$category][] = [
                'label' => $name,
                'value' => $roleHasPermission['permission_id']
            ];
        }

        foreach ($roles as $idx => $role) {
            $roles[$idx]['permission_list'] = $roleIdPermissionListMap[$role['id']] ?? [];
        }

        return $this->jsonResponse($roles);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $this->checkAccessPermission('Administer roles & permissions');

        $rules = [
            'name' => ['required', 'string', new NotExists('roles', 'name')],
            'permission_id_list' => ['present', 'array'],
            'permission_id_list.*' => ['exists:permissions,id'],
        ];

        $this->validate($request, $rules);

        $role = Role::query()->create([
            'name' => $request->get('name'),
            'guard_name' => 'api',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        assert($role instanceof Role);
        $role->permissions()->sync($request->get('permission_id_list'));

        return $this->jsonResponse([], 1);
    }

    /**
     * 单条记录
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $role = Role::query()->where('id', $id)->firstOrFail();
        assert($role instanceof Role);

        $role['permission_list'] = $role->permissions()->get(['id as value', 'name as label'])->toArray();

        return $this->jsonResponse($role);
    }

    /**
     * 更新
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {

        $this->checkAccessPermission('Administer roles & permissions');

        $rules = [
            'name' => ['required', 'string', new NotExists('roles', 'name', [$id])],
            'permission_id_list' => ['present', 'array'],
            'permission_id_list.*' => ['exists:permissions,id'],
        ];
        $this->validate($request, $rules);

        $role = Role::query()->where('id', $id)->firstOrFail();

        assert($role instanceof Role);
        $role->update(['name' => $request->get('name')]);
        $role->permissions()->sync($request->get('permission_id_list'));

        return $this->jsonResponse();
    }

    /**
     * 删除
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        $this->checkAccessPermission('Administer roles & permissions');

        $role = Role::query()->where('id', $id)->firstOrFail();

        assert($role instanceof Role);
        $role->permissions()->detach();
        $role->delete();

        return $this->jsonResponse();
    }
}
