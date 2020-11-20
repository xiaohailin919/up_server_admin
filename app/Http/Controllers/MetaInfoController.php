<?php

namespace App\Http\Controllers;

use App\Models\MySql\DataRole;
use App\Models\MySql\PublisherGroup;
use App\User;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class MetaInfoController extends ApiController
{
    /**
     * 用户元数据
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        $data = User::query()->select(['id as value', 'name as label'])
            ->where('status', User::STATUS_RUNNING)
            ->get()->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 角色元数据
     *
     * @return JsonResponse
     */
    public function role(): JsonResponse
    {
        $data = Role::query()->select(['id as value', 'name as label'])
            ->get()->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 数据权限角色，一二级嵌套
     * {
     *     "label": "管理员",
     *     "value": 1,
     *     "children": [
     *         {
     *             "label": "管理员",
     *             "value": 1,
     *             "children": []
     *         }
     *     ]
     * }
     * @return JsonResponse
     */
    public function dataRoleNesting(): JsonResponse
    {
        $data = DataRole::query()->select(['id', 'name', 'type'])
            ->orderBy('type')
            ->get()->toArray();
        $res = [];
        $typeMap = DataRole::getTypeMap();

        /* 第二层 */
        $tmp = [];
        foreach ($data as $datum) {
            $tmp[$datum['type']][] = ['label' => $datum['name'], 'value' => $datum['id'], 'children' => []];
        }
        /* 第一层 */
        foreach ($typeMap as $type => $name) {
            $res[] = ['label' => $name, 'value' => $type, 'children' => $tmp[$type] ?? []];
        }

        return $this->jsonResponse($res);
    }


    /**
     * 数据权限角色，角色用户列表
     * {
     *     "value": 1,
     *     "label": "服务端组",
     *     "user_name_list": [
     *         "刘政国","贺鹏飞","刘岳峰","叶卓豪"
     *     ],
     *     "type": 5
     * }
     * @return JsonResponse
     */
    public function dataRoleSecondary(): JsonResponse
    {
        $roles = DataRole::query()
            ->leftJoin('data_role_user_relationship as drur', 'drur.role_id', '=', 'data_role.id')
            ->leftJoin('users', 'users.id', '=', 'drur.user_id')
            ->select('data_role.name as label', 'data_role.id as value', 'data_role.type')
            ->selectRaw('GROUP_CONCAT(users.name) as user_name_list')
            ->groupBy(['data_role.name', 'data_role.id', 'data_role.type'])
            ->where('data_role.type', '!=', DataRole::TYPE_ADMIN)
            ->orderBy('type')
            ->get()->toArray();

        foreach ($roles as &$role) {
            $tmp = explode(',', $role['user_name_list']);
            $role['user_name_list'] = $tmp == [""] ? [] : $tmp;
        }

        return $this->jsonResponse($roles);
    }

    /**
     * 开发者群组
     *
     * @return JsonResponse
     */
    public function publisherGroup(): JsonResponse
    {
        $data = PublisherGroup::query()
            ->select(['id as value', 'name as label'])
            ->get()->toArray();
        return $this->jsonResponse($data);
    }
}
