<?php

namespace App\Console\Commands;

use App\Services\Permission as PermissionService;
use \Spatie\Permission\Models\Permission as PermissionModel;
use Illuminate\Console\Command;

class Permission extends Command
{
    protected $signature = 'permission:add';

    protected $description = 'Add the new permissions created in the config.';

    public function handle()
    {
        /* 已添加的所有权限 */
        $permissionList = array_column(PermissionModel::all(['name'])->toArray(), 'name');

        /* permission 文件中配置的所有权限，旧版本 Permission 的权限必定加入过了，因此不予以加入 */
        $writtenPermissions = config('permission.map');

        $insertion = [];

        foreach ($writtenPermissions as $controller => $permissionMap) {
            foreach ($permissionMap as $action => $permission) {
                if (!in_array($permission, $permissionList, true)) {
                    $insertion[] = [
                        'name' => $permission,
                        'guard_name' => 'web',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        if (PermissionModel::query()->insert($insertion)) {
            $this->info('Permission add successfully:');
            foreach ($insertion as $item) {
                $this->info($item['name']);
            }
        } else {
            $this->info('Permission add failed!');
        }
    }
}
