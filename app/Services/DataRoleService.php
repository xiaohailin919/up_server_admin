<?php


namespace App\Services;

use App\Models\MySql\DataRole;
use DB;

class DataRoleService
{
    public static function convertToViewModel(array $dataRoles): array
    {
        $ids = array_column($dataRoles, 'id');

        /* 每个角色绑定的用户 */
        $roleUsers = DB::query()
            ->from('data_role_user_relationship as t1')
            ->leftJoin('users as t2', 't2.id', '=', 't1.user_id')
            ->select(['t1.role_id', 't2.id'])
            ->selectRaw("IFNULL(t2.name ,'-') as name")
            ->whereIn('t1.role_id', $ids)
            ->get();
        $roleIdUserMap = [];
        foreach ($roleUsers as $roleUser) {
            $roleIdUserMap[$roleUser['role_id']][] = ['id' => $roleUser['id'], 'name' => $roleUser['name']];
        }

        /* 每个角色绑定的开发者 */
        $rolePublishers = DB::query()
            ->from('publisher_data_permission as t1')
            ->leftJoin('publisher as t2', 't2.id', '=', 't1.publisher_id')
            ->select(['t1.target_id as role_id', 't1.publisher_id as id'])
            ->selectRaw("IFNULL(t2.name, '-') as name")
            ->where('t1.target_type', DataRole::class)
            ->whereIn('t1.target_id', $ids)
            ->get();
        $roleIdPublisherMap = [];
        foreach ($rolePublishers as $rolePublisher) {
            $roleIdPublisherMap[$rolePublisher['role_id']][] = ['id' => $rolePublisher['id'], 'name' => $rolePublisher['name']];
        }

        foreach ($dataRoles as &$dataRole) {
            $dataRole['user_list'] = $roleIdUserMap[$dataRole['id']] ?? [];
            $dataRole['publisher_list'] = $roleIdPublisherMap[$dataRole['id']] ?? [];
        }

        return $dataRoles;
    }
}