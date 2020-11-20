<?php

namespace App\Http\Controllers;

use App\Events\UpdatePublisher;
use App\Helpers\Export;
use App\Models\MySql\DataRole;
use App\Models\MySql\FirmAdapter;
use App\Models\MySql\Network;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\PublisherApiInfo;
use App\Models\MySql\PublisherGroupRelationship;
use App\Services\Publisher as PublisherService;
use App\Models\MySql\PublisherGroup;
use App\Services\UserService;
use App\User;
use App\Utils\SHAHasher;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Rule;
use Log;

class PublisherApiController extends ApiController
{
    /**
     * 开发者列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->getData($request);

        $data['list'] = PublisherService::convertToViewModel($data['list'], false);

        return $this->jsonResponse($data);
    }

    public function getData(Request $request): array
    {
        $query = Publisher::query()
            ->from('publisher as t1')
            ->leftJoin('publisher_group_relationship as t2', 't2.publisher_id', '=', 't1.id')
            ->leftJoin('data_role_user_permission as t3', 't3.publisher_id', '=', 't1.id');

        $query = $request->input('inheritance_type', 1) == 1
            ? $query->select([
                't1.id', 't1.name', 't1.email', 't1.level', 't1.status', 't1.note', 't1.qq', 't1.skype', 't1.wechat', 't1.mode',
                't1.note_channel', 't1.company', 't1.contact', 't1.phone_number', 't1.create_time', 't1.sub_account_parent'
            ])->groupBy([
                't1.id', 't1.name', 't1.email', 't1.level', 't1.status', 't1.note', 't1.qq', 't1.skype', 't1.wechat', 't1.mode',
                't1.note_channel', 't1.company', 't1.contact', 't1.phone_number', 't1.create_time', 't1.sub_account_parent'
            ])
            : $query->select(['t1.sub_account_parent'])->groupBy(['t1.sub_account_parent', 't1.create_time']);
        if ($request->has('id')) {
            $query->where('t1.id', $request->query('id'));
        }
        if ($request->has('name')) {
            $query->where('t1.name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->has('email')) {
            $query->where('t1.email', $request->query('email'));
        }
        if ($request->has('note')) {
            $query->where('t1.note', 'like', '%' . $request->query('note') . '%');
        }
        if (array_key_exists($request->query('status', -1), Publisher::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }
        if (array_key_exists($request->query('channel', -1), Publisher::getChannelMap())) {
            $query->where('t1.channel_id', $request->query('channel'));
        }
        if (array_key_exists($request->query('publisher_group', -1), PublisherGroup::getPublisherGroupIdNameMap())) {
            $query->where('t2.publisher_group_id', $request->query('publisher_group'));
        }
        if (array_key_exists($request->query('level', -1), Publisher::getLevelMap())) {
            $query->where('t1.level', $request->query('level'));
        }
        if (in_array($request->query('data_permission', -1), [1, 2], false)) {
            $query->where('t1.data_permission_switch', $request->query('data_permission'));
        }
        if ($request->query('related', -1) == 1) {        // 1 代表未关联
            $query->whereNull('t3.publisher_id');
        } else if ($request->query('related', -1) == 2) { // 2 代表已关联
            $query->whereNotNull('t3.publisher_id');
        }
        if ($request->query('business_person', -1) > 0) {
            $businessPublishers = UserService::getPublisherIdByDisplayType($request->query('business_person'), DataRole::DISPLAY_TYPE_BUSINESS);
        }
        if ($request->query('operator', -1) > 0) {
            $operatorPublishers = UserService::getPublisherIdByDisplayType($request->query('operator'), DataRole::DISPLAY_TYPE_OPERATOR);
        }
        if (isset($businessPublishers, $operatorPublishers)) {
            $query->whereIn('t1.id', array_values(array_intersect($businessPublishers, $operatorPublishers)));
        } else if (isset($businessPublishers)) {
            $query->whereIn('t1.id', $businessPublishers);
        } else if (isset($operatorPublishers)) {
            $query->whereIn('t1.id', $operatorPublishers);
        }

        // 父子账号
        if ($request->query('inheritance_type', -1) == 2) {
            /* 先把子账号按照上面的规则搜出来 */
            $parentIds = array_column($query->orderByDesc('t1.create_time')->get()->toArray(), 'sub_account_parent');
            $query = Publisher::query()->from('publisher as t1')
                ->select([
                    't1.id', 't1.name', 't1.email', 't1.level', 't1.status', 't1.note', 't1.qq', 't1.skype', 't1.wechat',
                    't1.note_channel', 't1.company', 't1.contact', 't1.phone_number', 't1.create_time', 't1.sub_account_parent'
                ])
                ->whereIn('t1.id', $parentIds);
        } else {
            $query->where('t1.sub_account_parent', 0);
        }

        $res = $query->orderByDesc('t1.create_time')
            ->paginate($request->query('page_size', 20), ['*'], 'page_no', $request->query('page_no', 1));

        return $this->parseResByPaginator($res);
    }

    public function show($id): JsonResponse
    {
        $publisher = Publisher::query()
            ->leftJoin('users', 'users.id', '=', 'publisher.admin_id')->where('publisher.id', $id)
            ->firstOrFail(['publisher.*', 'users.name as admin_name']);
        assert($publisher instanceof Publisher);

        /* 处理 */
        $publisher['level'] = $publisher['level'] == 0 ? '' : $publisher['level'];
        $publisher['phone'] = $publisher['phone_number'];
        $publisher['channel_note'] = $publisher['note_channel'];

        /* 有权限的广告平台数量 */
        $publisher['network_firm_count'] = empty($publisher['allow_firms'])
            ? count(Publisher::DEFAULT_ALLOW_FIRMS)
            : count(json_decode($publisher['allow_firms'], true));
        $publisher['all_network_firm_count'] = count(NetworkFirm::getNwFirmMap());

        /* API 相关配置 */
        $publisherApiInfo = PublisherApiInfo::query()->where('publisher_id', $publisher['id'])->first();
        $publisher['key'] = '';
        $publisher['api_switch'] = $publisher['mode'] == Publisher::MODE_WHITE ? Publisher::API_SWITCH_ON : Publisher::API_SWITCH_OFF;
        $publisher['device_data_switch'] = Publisher::DEVICE_DATA_SWITCH_OFF;
        if ($publisherApiInfo !== null) {
            $publisher['key'] = $publisherApiInfo['publisher_key'];
            $publisher['api_switch'] = $publisherApiInfo['up_api_permission'];
            $publisher['device_data_switch'] = $publisherApiInfo['up_device_permission'];
        }
        /* 间接刷历史数据，数据错误当其他地方会用 mode 过滤所以不会带来太大影响，所以在每次修改的时候对数据进行修正：所有黑盒开发者，API 权限都关掉 */
        if ($publisher['mode'] == Publisher::MODE_BLACK) {
            $publisher['api_switch'] = Publisher::API_SWITCH_OFF;
            $publisher['device_data_switch'] = Publisher::DEVICE_DATA_SWITCH_OFF;
        }

        /* 开发者群组信息 */
        $publisherGroupIds = PublisherGroupRelationship::query()->where('publisher_id', $publisher['id'])->get(['publisher_group_id'])->toArray();
        $publisher['publisher_group_id_list'] = array_column($publisherGroupIds, 'publisher_group_id');

        /* 关联数据权限角色 ID */
//        $dataRoles = $publisher->dataRoles()->get();
//        $businesses = $operators = $otherPersons = [];
//        foreach ($dataRoles as $dataRole) {
//            switch ($dataRole['pivot']['display_type']) {
//                case DataRole::DISPLAY_TYPE_BUSINESS:
//                    $businesses[] = $dataRole['id'];
//                    break;
//                case DataRole::DISPLAY_TYPE_OPERATOR:
//                    $operators[] = $dataRole['id'];
//                    break;
//                case DataRole::DISPLAY_TYPE_OTHER:
//                    $otherPersons[] = $dataRole['id'];
//                    break;
//            }
//        }
//        $publisher['business_person_id_list'] = $businesses;
//        $publisher['operator_id_list']        = $operators;
//        $publisher['other_person_id_list']    = $otherPersons;

        /* 关联数据权限用户 ID */
        $users = $publisher->users()->where('status', User::STATUS_RUNNING)->get();
        $businesses = $operators = $otherPersons = [];
        foreach ($users as $user) {
            switch ($user['pivot']['display_type']) {
                case DataRole::DISPLAY_TYPE_BUSINESS:
                    $businesses[] = $user['id'];
                    break;
                case DataRole::DISPLAY_TYPE_OPERATOR:
                    $operators[] = $user['id'];
                    break;
                case DataRole::DISPLAY_TYPE_OTHER:
                    $otherPersons[] = $user['id'];
                    break;
            }
        }
        $publisher['business_person_id_list'] = $businesses;
        $publisher['operator_id_list']        = $operators;
        $publisher['other_person_id_list']    = $otherPersons;

        unset(
            $publisher['system'], $publisher['channel_id'], $publisher['api_key'], $publisher['phone_number'],
            $publisher['wechat'], $publisher['qq'], $publisher['skype'], $publisher['country'], $publisher['address'],
            $publisher['message'], $publisher['source'], $publisher['source_other'], $publisher['language'],
            $publisher['report_timezone'], $publisher['allow_firms'],
            $publisher['sub_account_rule'], $publisher['sub_account_distribution'],
            $publisher['check_mail_status'], $publisher['admin_id'], $publisher['migrate_4']
        );

        return $this->jsonResponse($publisher);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('publisher_edit');

        $rules = [
            'status'                    => ['required', Rule::in(array_keys(Publisher::getStatusMap()))],
            'level'                     => ['nullable', Rule::in(array_keys(Publisher::getLevelMap()))],
            'currency'                  => ['nullable', 'in:CNY,USD,JPY'],
            'mode'                      => ['required', 'in:1,2'],
            'data_permission_switch'    => ['required', 'in:1,2'],
            'api_switch'                => ['required', 'in:1,2'],
            'device_data_switch'        => ['required', 'in:1,2'],
            'report_import_switch'      => ['required', 'in:1,2'],
            'my_offer_switch'           => ['required', 'in:1,2'],
            'unit_repeat_switch'        => ['required', 'in:1,2'],
            'sub_account_switch'        => ['required', 'in:1,2'],
            'distribution_switch'       => ['required', 'in:1,2'],
            'network_multiple_switch'   => ['required', 'in:1,2'],
            'report_timezone_switch'    => ['required', 'in:1,2'],
            'scenario_switch'           => ['required', 'in:1,2'],
            'roi_switch'                => ['required', 'in:1,2'],
            'adx_switch'                => ['required', 'in:1,2'],
            'adx_unit_switch'           => ['required', 'in:1,2'],
            'fb_inh_hb_switch'          => ['required', 'in:1,2'],
            'publisher_group_id_list'   => ['nullable', 'array'],
            'publisher_group_id_list.*' => ['exists:publisher_group,id'],
            'business_person_id_list'   => ['nullable', 'array'],
            'business_person_id_list.*' => ['exists:users,id'],
            'operator_id_list'          => ['nullable', 'array'],
            'operator_id_list.*'        => ['exists:users,id'],
            'other_person_id_list'      => ['nullable', 'array'],
            'other_person_id_list.*'    => ['exists:users,id'],
        ];
        $this->validate($request, $rules);

        $publisher = Publisher::query()->where('id', $id)->firstOrFail();
        assert($publisher instanceof Publisher);

        /* 先暂存 PublisherApi 表的信息 */
        $apiSwitch = $request->input('api_switch');
        $deviceDataSwitch = $request->input('device_data_switch');

        $updateData = $request->all([
            'company', 'contact','note', 'status', 'mode', 'data_permission_switch',
            'report_import_switch', 'my_offer_switch', 'unit_repeat_switch', 'sub_account_switch',
            'distribution_switch', 'network_multiple_switch', 'report_timezone_switch', 'scenario_switch', 'roi_switch',
            'adx_switch', 'adx_unit_switch','fb_inh_hb_switch'
        ]);

        $updateData['level'] = array_key_exists($request->input('level'), Publisher::getLevelMap()) ? $request->input('level') : 0;
        $updateData['phone_number'] = $request->input('phone', '');
        $updateData['currency'] = $publisher['currency'] == 'USD' ? $request->input('currency') : $publisher['currency'];
        $updateData['note_channel'] = $request->input('channel_note', '');
        $updateData['admin_id'] = auth()->id();
        $updateData['update_time'] = time();

        /* 报表时区关闭 */
        if ($updateData['report_timezone_switch'] != $publisher['report_timezone_switch']) {
            $updateData['report_timezone'] = 108;
        }
        /* 白盒 / 黑盒 */
        /* 所有黑盒开发者，API 权限都关掉 */
        if ($updateData['mode'] == Publisher::MODE_BLACK) {
            $updateData['report_timezone_switch'] = Publisher::REPORT_TIMEZONE_SWITCH_OFF;
            $updateData['report_timezone'] = 0;
            $updateData['report_import_switch'] = Publisher::REPORT_IMPORT_SWITCH_OFF;

            $apiSwitch = Publisher::API_SWITCH_OFF;
            $deviceDataSwitch = Publisher::DEVICE_DATA_SWITCH_OFF;
        }
        /* 所有 API 权限关闭的，设备权限也关闭 */
        if ($apiSwitch == Publisher::API_SWITCH_OFF) {
            $deviceDataSwitch = Publisher::DEVICE_DATA_SWITCH_OFF;
        }
        /* 开发者状态 */
        if ($updateData['status'] != $publisher['status'] && in_array($updateData['status'], [Publisher::STATUS_LOCKED, Publisher::STATUS_PENDING], false)) {
            $publisher->updateStatusByParentId($id, $updateData['status']);
        }
        /* MyOffer 开关：若开启 MyOffer 则创建一个 Network 账号 */
        if ($updateData['my_offer_switch'] != $publisher['my_offer_switch'] && $updateData['my_offer_switch'] == Publisher::MY_OFFER_SWITCH_ON) {
            $query = Network::query();
            if(!$query->where("publisher_id", $id)->where("nw_firm_id", NetworkFirm::MYOFFER)->exists()){
                $query->insert([
                    'name'            => "My Offer",
                    'api_version'     => 1,
                    'status'          => 3,
                    'app_id'          => 0,
                    'open_api_status' => 3,
                    'update_time'     => time(),
                    'unit_switch'     => 2,
                    'publisher_id'    => $id,
                    'nw_firm_id'      => 35,
                    'create_time'     => time(),
                ]);
            }
        }
        /* 子账号开关 */
        if ($updateData['sub_account_switch'] == Publisher::SUB_ACCOUNT_SWITCH_OFF) {
            if ($updateData['sub_account_switch'] != $publisher['sub_account_switch']) {
                $publisher->updateStatusByParentId($id, Publisher::STATUS_LOCKED);
            }
            $updateData['distribution_switch'] = Publisher::DIST_SWITCH_OFF;
        }

        try {
            DB::beginTransaction();

            /* 数据权限开关 与 level 要求父子账号同步 */
            if ($publisher['sub_account_parent'] == 0) {
                /* 子账号应用相同的设置 */
                Publisher::query()->where('sub_account_parent', $id)->update([
                    'level' => $updateData['level'],
                    'data_permission_switch' => $updateData['data_permission_switch'],
                    'update_time' => time(),
                    'admin_id' => auth()->id(),
                ]);
            } else {
                /* 子账号的话，不作更改 */
                unset($updateData['level'], $updateData['data_permission_switch']);
            }

            /* 处理开发者群组 */
            $publisherGroupIds = $request->input('publisher_group_id_list', []);
            $relationships = PublisherGroupRelationship::query()->where('publisher_id', $id)->get();
            foreach ($relationships as $relationship) {
                if (in_array($relationship['publisher_group_id'], $publisherGroupIds, false)) {
                    continue;
                }
                $relationship->delete();
            }
            foreach ($publisherGroupIds as $publisherGroupId) {
                PublisherGroupRelationship::query()->firstOrCreate(['publisher_group_id' => $publisherGroupId, 'publisher_id' => $publisher['id']]);
            }

            /* 处理 API Info */
            $api = PublisherApiInfo::query()->where('publisher_id', $id)->first();
            if ($api == null) {
                if ($apiSwitch == Publisher::API_SWITCH_ON) {
                    PublisherApiInfo::query()->create([
                        'publisher_id'         => $id,
                        'publisher_key'        => (new SHAHasher())->make(microtime().mt_rand()),
                        'up_api_permission'    => $apiSwitch,
                        'up_device_permission' => $deviceDataSwitch,
                        'update_time'          => date('Y-m-d H:i:s'),
                        'create_time'          => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                $api->update([
                    'up_api_permission'    => $apiSwitch,
                    'up_device_permission' => $deviceDataSwitch,
                    'update_time'          => date('Y-m-d H:i:s'),
                ]);
            }

            /* 绑定与解绑数据角色权限 */
//            $roleDataPermission = [];
//            foreach ($request->input('business_person_id_list', []) as $item) {
//                $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_BUSINESS];
//            }
//            foreach ($request->input('operator_id_list', []) as $item) {
//                $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OPERATOR];
//            }
//            foreach ($request->input('other_person_id_list', []) as $item) {
//                $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OTHER];
//            }
//            $publisher->dataRoles()->sync($roleDataPermission);

            /* 绑定与解绑用户数据权限 */
            $userDataPermission = [];
            foreach ($request->input('business_person_id_list', []) as $item) {
                $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_BUSINESS];
            }
            foreach ($request->input('operator_id_list', []) as $item) {
                $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OPERATOR];
            }
            foreach ($request->input('other_person_id_list', []) as $item) {
                $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OTHER];
            }
            /* 将已删除用户查出来，不需要解绑已删除用户 */
            $deleteUsers = $publisher->users()->where('status', User::STATUS_DELETED)->get()->toArray();
            foreach ($deleteUsers as $deleteUser) {
                $userDataPermission[$deleteUser['id']] = ['display_type' => $deleteUser['pivot']['display_type']];
            }

            $publisher->users()->sync($userDataPermission);

            /* 更新 */
            $publisher->update($updateData);

            /* 触发更新事件 */
            Event::fire(new UpdatePublisher($id, $updateData));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Publisher update failed: " . $id . ' Error:' . $e->getMessage());
            return $this->jsonResponse([
                'notice'  => 'Save failed, all operation rollback.',
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'trace'   => $e->getTrace(),
                'line'    => $e->getLine(),
                ], 9995);
        }

        return $this->jsonResponse();
    }

    /**
     * 开发者批量编辑功能
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function multipleUpdate(Request $request) {
        $rules = [
            'level'     => ['nullable', Rule::in(array_keys(Publisher::getLevelMap()))],
            'id_list'   => ['required', 'array'],
            'id_list.*' => ['required', 'exists:publisher,id'],
            'business_person_id_list'   => ['nullable', 'array'],
            'business_person_id_list.*' => ['exists:users,id'],
            'operator_id_list'          => ['nullable', 'array'],
            'operator_id_list.*'        => ['exists:users,id'],
            'other_person_id_list'      => ['nullable', 'array'],
            'other_person_id_list.*'    => ['exists:users,id'],
        ];

        $this->validate($request, $rules);

        try {
            DB::beginTransaction();

            /* 处理 Level */
            $query = Publisher::query()->whereIn('id', $request->input('id_list'));
            if (array_key_exists($request->input('level'), Publisher::getLevelMap())) {
                $query->update([
                    'level'       => $request->input('level'),
                    'update_time' => time(),
                    'admin_id'    => auth()->id(),
                ]);
            }

            /* 处理角色数据权限，只能解绑相同类型的 */
//            $roleDataPermission = [];
//            $displayType = -1;
//            if ($request->input('business_person_id_list', []) != []) {
//                foreach ($request->input('business_person_id_list', []) as $item) {
//                    $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_BUSINESS];
//                    $displayType = DataRole::DISPLAY_TYPE_BUSINESS;
//                }
//            } else if ($request->input('operator_id_list', []) != []) {
//                foreach ($request->input('operator_id_list', []) as $item) {
//                    $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OPERATOR];
//                    $displayType = DataRole::DISPLAY_TYPE_OPERATOR;
//                }
//            } else if ($request->input('other_person_id_list', []) !=[]) {
//                foreach ($request->input('other_person_id_list', []) as $item) {
//                    $roleDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OTHER];
//                    $displayType = DataRole::DISPLAY_TYPE_OTHER;
//                }
//            }
//            if ($roleDataPermission != []) {
//                $publishers = $query->get();
//                foreach ($publishers as $publisher) {
//                    assert($publisher instanceof Publisher);
//                    $publisher->dataRoles()->wherePivot('display_type', $displayType)->sync($roleDataPermission);
//                }
//            }

            /* 处理角色数据权限，只能解绑相同类型的，已删除用户前端是不传过来的，需要查出来写入 */
            $userDataPermission = [];
            $displayType = -1;
            if ($request->input('business_person_id_list', []) != []) {
                foreach ($request->input('business_person_id_list', []) as $item) {
                    $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_BUSINESS];
                    $displayType = DataRole::DISPLAY_TYPE_BUSINESS;
                }
            } else if ($request->input('operator_id_list', []) != []) {
                foreach ($request->input('operator_id_list', []) as $item) {
                    $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OPERATOR];
                    $displayType = DataRole::DISPLAY_TYPE_OPERATOR;
                }
            } else if ($request->input('other_person_id_list', []) !=[]) {
                foreach ($request->input('other_person_id_list', []) as $item) {
                    $userDataPermission[$item] = ['display_type' => DataRole::DISPLAY_TYPE_OTHER];
                    $displayType = DataRole::DISPLAY_TYPE_OTHER;
                }
            }
            if ($userDataPermission != []) {
                $publishers = $query->get();
                foreach ($publishers as $publisher) {
                    assert($publisher instanceof Publisher);

                    $tmpUserDataPermission = $userDataPermission;

                    /* 如果该开发者其他角色已绑定该用户，不进行绑定 */
                    $bindUsersIds = $publisher->users()->wherePivot('display_type', '!=', $displayType)->whereIn('users.id', array_keys($tmpUserDataPermission))->get()->getQueueableIds();
                    foreach ($bindUsersIds as $bindUsersId) {
                        unset($tmpUserDataPermission[$bindUsersId]);
                    }

                    /* 该开发者绑定的已删除用户查出来 */
                    $deletedUserIds = $publisher->users()
                        ->wherePivot('display_type', $displayType)
                        ->where('status', User::STATUS_DELETED)
                        ->get()->getQueueableIds();
                    /* 加入到待绑定的用户列表中 */
                    foreach ($deletedUserIds as $deletedUserId) {
                        $tmpUserDataPermission[$deletedUserId] = ['display_type' => $displayType];
                    }
                    $publisher->users()->wherePivot('display_type', $displayType)->sync($tmpUserDataPermission);
                }
            }

            DB::commit();

            return $this->jsonResponse();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Publisher multiple update failed: " . implode(',', $request->input('id_list', [])) . ' Error:' . $e->getMessage());
            return $this->jsonResponse([
                'notice'  => 'Save failed, all operation rollback.',
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'trace'   => $e->getTrace(),
                'line'    => $e->getLine(),
            ], 9995);
        }
    }


    /**
     * 获取开发者广告平台权限列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNetworkFirm(Request $request): JsonResponse
    {
        $publisher = Publisher::query()->where('id', $request->input('id', 0))->firstOrFail();

        $res = empty($publisher['allow_firms'])
            ? Publisher::DEFAULT_ALLOW_FIRMS
            : json_decode($publisher['allow_firms'], true);

        foreach ($res as $key => $datum) {
            $res[$key] = (int)$datum;
        }

        return $this->jsonResponse($res);
    }

    public function updateNetworkFirm(Request $request): JsonResponse
    {
        $rule = [
            'id'                => ['exists:publisher,id'],
            'nw_firm_id_list'   => ['nullable', 'array'],
            'nw_firm_id_list.*' => ['exists:network_firm,id'],
        ];
        $this->validate($request, $rule);

        $firmList = (array)$request->input('nw_firm_id_list', []);

        /* 旧版本只能使用 27 之前的厂商 */
        if (Publisher::query()->where('id', $request->input('id'))->value('migrate_4') < 3) {
            foreach ($firmList as $key => $value) {
                if ($value > 27) {
                    unset($firmList[$key]);
                }
            }
        }

        /* 如果没绑定，就绑默认 */
        if (empty($firmList)) {
            $firmList = Publisher::DEFAULT_ALLOW_FIRMS;
        }

        Publisher::query()->where('id', $request->input('id'))->update([
            'allow_firms' => json_encode($firmList),
            'update_time' => time(),
            'admin_id' => auth()->id(),
        ]);

        return $this->jsonResponse();
    }

    /**
     * 登录某个用户开发者后台
     *
     * @param Request $request
     * @return string
     */
    public function login(Request $request){

        $id       = $request->input('id', 0);

        $operableUsers = UserService::getPublisherIdListByUserId(auth()->id());
        if (!in_array($id, $operableUsers, false)) {
            $this->jsonResponse([], 9003);
        }

        $publisher = Publisher::query()->find($id);

        if ($publisher == null) {
            return $this->jsonResponse(['id' => 'PublisherService id is not found: ' . $id], 13000);
        }
        if (empty($publisher['email'])) {
            return $this->jsonResponse(['id' => 'PublisherService email is not found: ' . $id], 13001);
        }

        $timestamp = time();
        $adminId = $request->query('type', '') == 'self' ? 0 : auth()->id();
        $loginParam = [
            "admin_id"  => $adminId,
            "email"     => $publisher['email'],
            "timestamp" => $timestamp,
            "sign"      => md5($adminId . '/' . $publisher['email'] . '/' . $timestamp . '/' . env('LOGIN_TOKEN_FOR_ADMIN')),
        ];
        if($request->has('redirect')){
            $loginParam['redirect'] = $request->query('redirect');
        }

        $url = env('DN_UP_APP');
        if ($publisher['channel_id'] == Publisher::CHANNEL_233) {
            $url = 'http://' . str_replace('admin.', 'app.', env('CHANNEL_233_HOST')) . '/';
        }else if ($publisher['channel_id'] == Publisher::CHANNEL_DLADS) {
            $url = 'http://' . str_replace('admin.', 'app.', env('CHANNEL_DLADS_HOST')) . '/';
        }
        if ($request->query('env', '') === 'pre') {
            $url = 'http://pre-app.toponad.com/';
        }

        $loginUrl = $url . 'm/auth-redirect?' . http_build_query($loginParam);

        $response = new Response();
        $response->header('Location', $loginUrl);
        return $response;
    }

    /**
     * @param Request $request
     */
    public function export(Request $request) {
        $request->query->set('page_size', 5000);
        $data = $this->getData($request);

        $data['list'] = PublisherService::convertToViewModel($data['list'], true);

        $headerNameMap = [
            'id'                   => '开发者 ID',
            'name'                 => '名称',
            'company'              => '公司',
            'contact'              => '联系人',
            'email'                => '邮箱',
            'phone_number'         => '电话',
            'level'                => '等级',
            'business_person_list' => '商务',
            'operator_list'        => '运营',
            'publisher_group'      => '开发者群组',
            'status'               => '状态',
            'wechat'               => '微信',
            'qq'                   => 'QQ',
            'skype'                => 'Skype',
            'note'                 => '备注',
            'note_channel'         => 'Channel Note',
            'create_time'          => '创建时间',
        ];

        Export::excel($headerNameMap, $data['list']);
    }

    /**
     * 获取拥有自定义广告平台的开发者作为元数据
     *
     * @return JsonResponse
     */
    public function metaCustomNetwork(): JsonResponse
    {
        $data = NetworkFirm::query()
            ->from(NetworkFirm::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->select('t1.publisher_id as value')
            ->selectRaw("IFNULL(t2.name, 'ALL') as label")
            ->orderBy('t1.publisher_id')
            ->groupBy(['t1.publisher_id', 't2.name'])
            ->get()->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 获取拥有自定义 Adapter 的开发者作为元数据
     *
     * @return JsonResponse
     */
    public function metaCustomAdapter(): JsonResponse
    {
        $data = FirmAdapter::query()
            ->from(FirmAdapter::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->select('t1.publisher_id as value')
            ->selectRaw("IFNULL(t2.name, 'ALL') as label")
            ->orderBy('t1.publisher_id')
            ->groupBy(['t1.publisher_id', 't2.name'])
            ->get()->toArray();
        return $this->jsonResponse($data);
    }
}
