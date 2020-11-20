<?php

namespace App\Services;

use App\Models\MySql\DataRole;
use App\Models\MySql\Publisher;
use App\User;

class UserService {

    /**
     * 根据用户 ID， 获取用户拥有数据权限的所有用户
     *
     * @param $userId
     * @return array
     */
    public static function getPublisherIdListByUserId($userId): array {
        if (empty($userId)) {
            return [];
        }

        $user = User::query()->where('id', $userId)->firstOrFail();
        assert($user instanceof User);

        /* 获取该开发者绑定的角色 */
//        $dataRoles = $user->dataRoles()->get();

        /* 判断绑定的角色里面是否有管理员 */
//        $adminRole = array_first($dataRoles->toArray(), static function ($value) {return $value['type'] == DataRole::TYPE_ADMIN;});
        /* 管理员返回所有开发者 */
        if ($user['type'] == DataRole::TYPE_ADMIN) {
            $publishers = Publisher::query()->get(['id'])->toArray();
            return array_column($publishers, 'id');
        }

        /* 获取用户自身绑定的开发者 */
        $userRelatedPublishers = $user->publishers()->get()->getQueueableIds();

        /* 获取这些角色拥有的开发者 */
//        $roleRelatedPublishers = [];
//        foreach ($dataRoles as $dataRole) {
//            assert($dataRole instanceof DataRole);
//            $tmpPublishers = $dataRole->publishers()->get()->getQueueableIds();
//            foreach ($tmpPublishers as $tmpPublisher) {
//                $roleRelatedPublishers[] = $tmpPublisher;
//            }
//        }

//        $relatedPublishers = array_merge($roleRelatedPublishers, $userRelatedPublishers);

        /* 获取未设置数据权限的开发者主账号 */
        $publicPublishers = Publisher::query()
            ->where('data_permission_switch', Publisher::DATA_PERMISSION_OFF)
            ->where('sub_account_parent', 0)
            ->get(['id'])->getQueueableIds();

        $relatedPublishers = array_merge($publicPublishers, $userRelatedPublishers);

        /* 获取这些拥有权限的开发者的子账号 */
        $subAccountPublishers = Publisher::query()->whereIn('sub_account_parent', $relatedPublishers)->get()->getQueueableIds();

        /* 取并集 */
        $res = array_values(array_unique(array_merge($relatedPublishers, $subAccountPublishers)));

        return empty($res) ? [] : $res;
    }

    /**
     * 根据用户的商务 Display Type 获取所有开发者 ID
     *
     * @param $businessId
     * @param $displayType
     * @return array
     */
    public static function getPublisherIdByDisplayType($businessId, $displayType): array
    {
        $user = User::query()->where('id', $businessId)->firstOrFail();
        assert($user instanceof User);

        /* 获取用户本身以该 Display Type 身份绑定的用户 */
        $relatePublisherIds = $user->publishers()->wherePivot('display_type', $displayType)->get()->getQueueableIds();

        /* 获取用户所有角色 */
        $dataRoles = $user->dataRoles()->get();

        /* 这些角色以 Display Type 绑定的用户 */
        $roleRelatePublisherIds = [];
        foreach ($dataRoles as $dataRole) {
            assert($dataRole instanceof DataRole);

            $tmp = $dataRole->publishers()->wherePivot('display_type', $displayType)->get()->getQueueableIds();
            foreach ($tmp as $item) {
                $roleRelatePublisherIds[] = $item;
            }
        }
        return array_unique(array_merge($roleRelatePublisherIds, $relatePublisherIds));
    }
}