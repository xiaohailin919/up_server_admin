<?php

namespace App\Services;

use App\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Nav
{
    /**
     * 通过权限列表获取导航列表
     *
     * @return array
     */
    private static function parseToList(): array
    {
        $navList = config('route');

        $user = \Auth::user();
        if ($user === null) {
            throw new HttpException(500, __('code.' . 9994), null, [], 9994);
        }
        assert($user instanceof User);

        $permissions = array_column($user->getAllPermissions()->toArray(), 'name');

        foreach ($navList as $i => $parentNav) {
            foreach ($parentNav['list'] as $j => $childNav) {
                if (!in_array($childNav['permission'], $permissions, true)) {
                    unset($navList[$i]['list'][$j]);
                }
            }
        }

        return $navList;
    }

    /**
     * 获取所有权限的导航列表
     * @return array
     */
    public static function getAllList(): array
    {
        return config('route');
    }

    /**
     * 获取当前用户的导航列表
     * @return array
     */
    public static function getList(): array
    {
        return self::parseToList();
    }

}
