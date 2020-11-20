<?php

namespace App\Services;

use App\Models\MySql\DataRole;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\Mongo\Publisher as PublisherMoModel;
use App\Models\MySql\Users;
use App\User;
use DB;

class Publisher
{
    public static function convertToViewModel($publishers, bool $isExport): array
    {
        /* 获取当前用户可操作的 publisher id 列表 */
        $operablePublisherIds = UserService::getPublisherIdListByUserId(auth()->id());

        $publisherIds = array_column($publishers, 'id');

//        return $publisherIds;

        /* 获取开发者的子账号，获取完之后更新一下 PublisherIds */
        $subAccounts = PublisherMyModel::query()->whereIn('sub_account_parent', $publisherIds)
            ->select([
                'id', 'name', 'email', 'level', 'status', 'note', 'qq', 'skype', 'wechat', 'note_channel',
                'sub_account_parent', 'company', 'contact', 'phone_number', 'create_time', 'mode'
            ])
            ->get()->toArray();
        $publisherIds = array_merge($publisherIds, array_column($subAccounts, 'id'));

        /* 获取列表中所有开发者到 User 的映射，若角色下没绑定用户，则跳过 */
        $publisherRelatedUsers = DB::query()->from('data_role_user_permission as drup')
            ->leftJoin('users', static function($subQuery) {
                $subQuery->on('users.id', '=', 'drup.target_id')
                    ->where('drup.target_type', '=', User::class);
            })
            ->where('users.status', '=', User::STATUS_RUNNING)
            ->where('drup.target_type', '=', User::class)
            ->get(['drup.publisher_id', 'drup.target_id', 'drup.display_type'])->toArray();
        /* 转成 Id 为 key 的 Map */
        $relatedUserMap = [];
        foreach ($publisherRelatedUsers as $publisherRelatedUser) {
            $relatedUserMap[$publisherRelatedUser['publisher_id']][] = [
                'id'   => $publisherRelatedUser['target_id'],
                'name' => Users::getName($publisherRelatedUser['target_id']),
                'type' => $publisherRelatedUser['display_type'],
            ];
        }

//        return $relatedUserMap;

        /* 获取用户和 Publisher Group 的映射 */
        $publisherRelatedGroups = DB::query()->from('publisher_group_relationship as t1')
            ->leftJoin('publisher_group as t2', 't2.id', '=', 't1.publisher_group_id')
            ->whereIn('t1.publisher_id', $publisherIds)
            ->get(['t1.publisher_id', 't2.id', 't2.name'])->toArray();
        /* 转成 Id 为 key 的 Map */
        $relatedGroupMap = [];
        foreach ($publisherRelatedGroups as $publisherRelatedGroup) {
            $relatedGroupMap[$publisherRelatedGroup['publisher_id']][] = [
                'label' => $publisherRelatedGroup['name'],
                'value' => $publisherRelatedGroup['id'],
            ];
        }

        /* 修饰子账号数据，注入子账号的 publisher group 信息，同时生成父子账号的 Map */
        $childrenMap = [];
        foreach ($subAccounts as &$subAccount) {
            $subAccount['operable'] = in_array($subAccount['id'], $operablePublisherIds, false) ? 1 : 2;
            $subAccount['level']  = '-';
            $subAccount['business_person_list'] = [];
            $subAccount['operator_list'] = [];
            $subAccount['publisher_group'] = $relatedGroupMap[$subAccount['id']] ?? [];
            $childrenMap[$subAccount['sub_account_parent']][] = $subAccount;
        }

        /* 正式修饰数据，注意：数据导出不需要子账号数据 */
        foreach ($publishers as &$publisher) {
            /* 数据权限标识 */
            $publisher['operable'] = in_array($publisher['id'], $operablePublisherIds, false) ? 1 : 2;

            /* 修饰普通信息 */
            $publisher['level']  = isset($publisher['level']) ? PublisherMyModel::getLevelName($publisher['level']) : '-';
            $publisher['note']   = $publisher['note']   ?? '-';
            $publisher['qq']     = $publisher['qq']     ?? '-';
            $publisher['skype']  = $publisher['skype']  ?? '-';
            $publisher['wechat'] = $publisher['wechat'] ?? '-';
            $publisher['note_channel'] = $publisher['note_channel'] ?? '-';
            $publisher['phone_number'] = str_pad(str_replace('-', ' ', $publisher['phone_number']), 20, ' ', STR_PAD_RIGHT);
            $publisher['status'] = $isExport ? PublisherMyModel::getStatusMap(true)[$publisher['status']] : $publisher['status'];
            $publisher['create_time'] = $isExport ? date('Y-m-d H:i:s', $publisher['create_time'] / 1000) : $publisher['create_time'];

            /* 注入子账号信息 */
            if (!$isExport) {
                $publisher['children'] = [];
                if (array_key_exists($publisher['id'], $childrenMap)) {
                    $publisher['children'] = $childrenMap[$publisher['id']];
                }
            }

            /* 注入商务人员列表与运营人员列表 */
            $businesses = $operators = [];
            if (array_key_exists($publisher['id'], $relatedUserMap)) {
                foreach ($relatedUserMap[$publisher['id']] as $item) {
                    if ($item['type'] == DataRole::DISPLAY_TYPE_BUSINESS) {
                        $businesses[] = $item['name'];
                    } else if ($item['type'] == DataRole::DISPLAY_TYPE_OPERATOR) {
                        $operators[] = $item['name'];
                    }
                }
            }
            $publisher['business_person_list'] = $isExport ? implode(', ', $businesses) : $businesses;
            $publisher['operator_list']        = $isExport ? implode(', ', $operators)  : $operators;

            /* 注入开发者群组信息，报表导出则注入名称字符串，子账号也要注入 */
            if ($isExport) {
                $publisherGroup = [];
                if (array_key_exists($publisher['id'], $relatedGroupMap)) {
                    foreach ($relatedGroupMap[$publisher['id']] as $item) {
                        $publisherGroup[]= $item['label'];
                    }
                }
                $publisher['publisher_group'] = implode(', ', $publisherGroup);
            } else {
                $publisher['publisher_group'] = $relatedGroupMap[$publisher['id']] ?? [];
            }
        }

        if ($isExport) {
            return $publishers;
        }

        return $publishers;
    }

    public static function sync($id)
    {
        $publisher = self::buildSyncData($id);
        $sync = new Sync('publisher');
        return $sync->handle($id, $publisher);
    }

    public static function queueSync($queueId, $id)
    {
        $publisher = self::buildSyncData($id);
        $sync = new QueueSync('publisher');
        return $sync->handle($queueId, $id, $publisher);
    }

    public static function buildSyncData($id)
    {
        $field = [
            'id as publisher_id',
            'email',
            'api_key',
            'system',
            'status'
        ];

        $publisherMyModel = new PublisherMyModel();

        $publisher = $publisherMyModel->getOne($field, ['id' => $id]);

        if (!$publisher) {
            return [];
        }

        $publisher['update_time'] = time();
        $publisher['status'] = ($publisher['status'] == PublisherMyModel::STATUS_RUNNING) ? PublisherMoModel::STATUS_RUNNING : PublisherMoModel::STATUS_STOP;

        return $publisher;
    }
}