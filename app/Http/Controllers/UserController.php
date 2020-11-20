<?php

namespace App\Http\Controllers;

use App\Models\MySql\DataRole;
use App\Rules\NotExists;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class UserController extends ApiController {

    /**
     * 用户列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /* 直接一次查询肯定是比多次查询快的，但是代码看起来有点累 */
        $query = User::query()
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
            ->leftJoin('roles as r', 'r.id', '=', 'mhr.role_id')
            ->groupBy(['users.id', 'users.name', 'users.email', 'users.created_at', 'users.type'])
            ->select(['users.id', 'users.name', 'users.email', 'users.created_at', 'users.type'])
            ->selectRaw('GROUP_CONCAT(r.name) as permission_role')
            ->where('users.status', '=', User::STATUS_RUNNING);
        if ($request->has('name')) {
            $query->where('users.name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->has('email')) {
            $query->where('users.email', $request->query('email'));
        }
        if (array_key_exists($request->query('type', -1), DataRole::getTypeMap())) {
            $query->where('users.type', '=', $request->query('type'));
        }
        $data = $query->paginate($request->query('page_size', 25), ['*'], 'page_no', $request->query('page_no', 1));

        $res = $this->parseResByPaginator($data);

        /* 修饰数据 */
        foreach ($res['list'] as &$user) {
            $user['type'] = $user['type'] == 0 ? '' : $user['type'];
            $user['create_time'] = $user['created_at'];
            unset($user['created_at']);
            /* 去一下重，因为数据角色 x 页面角色是笛卡尔积，会多出许多重复数据 */
            $roles = $user['permission_role'] == null ? [] : explode(',', $user['permission_role']);
            $roles = array_values(array_unique($roles));
            $user['permission_role'] = implode(',', $roles);
        }

        return $this->jsonResponse($res);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name'                      => ['required', 'string', 'max:120'],
            'email'                     => ['required', 'email', new NotExists('users', 'email')],
            'password'                  => ['required', 'min:6', 'string', 'confirmed'],
            'type'                      => ['required', Rule::in(array_keys(DataRole::getTypeMap()))],
            'data_role_id_list'         => ['nullable', 'array'],
            'data_role_id_list.*'       => ['required', 'exists:data_role,id'],
            'permission_role_id_list'   => ['required', 'array'],
            'permission_role_id_list.*' => ['required', 'exists:roles,id'],
        ];
        $this->validate($request, $rules);

        /* 创建用户 */
        $user = User::query()->create($request->only('email', 'name', 'password', 'type')); //Retrieving only the email and password data
        if (!$user instanceof User) {
            return $this->jsonResponse([], 9998, 'Create user failed');
        }

        /* 用户绑定角色 */
        $user->roles()->sync($request->input('permission_role_id_list'));

        /* 用户绑定数据权限角色 */
        if (array_key_exists($request->input('type'), DataRole::getTypeMap())) {
            if ($request->input('type') == DataRole::TYPE_ADMIN) {
                $user->dataRoles()->sync([1]);
            } else {
                $user->dataRoles()->sync($request->input('data_role_id_list'));
            }
        }

        return $this->show($user['id']);
    }

    /**
     * 元数据，不包括渠道方用户和未配置用户
     * http://yapi.toponad.com/project/18/interface/api/1121
     *
     * @return JsonResponse
     */
    public function meta() :JsonResponse {
        $users = User::query()
            ->leftJoin('model_has_roles as mhr', static function ($subQuery) {
                $subQuery->on('mhr.model_id', '=', 'users.id')
                    ->where('mhr.model_type', '=', User::class);
            })
            ->groupBy(['users.id', 'users.name', 'users.type'])
            ->where('users.type', '!=', 0)
            ->where('users.status', '=', User::STATUS_RUNNING)
            ->whereNotIn('mhr.role_id', [3, 4])
            ->orderBy('users.type')
            ->orderByRaw('CONVERT(users.name using gbk) asc')
            ->get(['users.id as value', 'users.name as label', 'users.type']);

        return $this->jsonResponse($users);
    }

    /**
     * 用户单条记录
     * http://yapi.toponad.com/project/18/interface/api/1094
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::query()->where('id', $id)->where('status', User::STATUS_RUNNING)->firstOrFail();
        assert($user instanceof User);

        $res = $user->toArray();
        $res['type'] = $res['type'] == 0 ? '' : $res['type'];

        /* 补数据角色数据 */
//        $res['data_role_id_list'] = $user->dataRoles()->get()->getQueueableIds();

        /* 补页面权限数据 */
        $roles = $user->roles()->get(['id'])->toArray();
        $res['permission_role_id_list'] = array_column($roles, 'id');
        return $this->jsonResponse($res);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'name'                      => ['required', 'string', 'max:120'],
            'email'                     => ['required', 'email', new NotExists('users', 'email', [$id])],
            'password'                  => ['nullable', 'min:6', 'string', 'confirmed'],
            'type'                      => ['required', Rule::in(array_keys(DataRole::getTypeMap()))],
            'data_role_id_list'         => ['nullable', 'array'],
            'data_role_id_list.*'       => ['required', 'exists:data_role,id'],
            'permission_role_id_list'   => ['required', 'array'],
            'permission_role_id_list.*' => ['required', 'exists:roles,id'],
        ];

        $this->validate($request, $rules);

        $user = User::query()->where('id', $id)->where('status', User::STATUS_RUNNING)->firstOrFail();
        assert($user instanceof User);

        $only = $request->input('password') == '' ? ['name', 'email', 'type'] : ['name', 'email', 'password', 'type'];
        $user->fill($request->only($only))->save();

        /* 处理页面权限 */
        $user->roles()->sync($request->input('permission_role_id_list'));

        /* 用户绑定数据权限角色 */
        if (array_key_exists($request->input('type'), DataRole::getTypeMap())) {
            if ($request->input('type') == DataRole::TYPE_ADMIN) {
                $user->dataRoles()->sync([1]);
            } else {
                $user->dataRoles()->sync($request->input('data_role_id_list'));
            }
        }

        return $this->show($user['id']);
    }

    public function destroy($id): JsonResponse
    {
        User::query()->where('id', $id)->update(['status' => User::STATUS_DELETED]);
        return $this->jsonResponse();
    }

    public function getEmailSignature(): JsonResponse
    {
        $key = 'email_signature_' . auth('api')->id();
        $data = json_decode(Redis::connection()->get($key), true);
        if (empty($data)) {
            $data = [
                'default'    => true,
                'name'       => '',
                'name_en'    => '',
                'title'      => '',
                'title_en'   => '',
                'phone'      => '',
                'email'      => '',
                'wechat'     => '',
                'skype'      => '',
                'avatar_url' => 'https://www.toponad.com/image/index/logo.png',
            ];
        } else {
            $data['default'] = false;
        }
        return $this->jsonResponse($data);
    }

    public function setEmailSignature(Request $request): JsonResponse
    {
        $rules = [
            'name'       => ['required', 'string'],
            'name_en'    => ['required', 'string'],
            'title'      => ['required', 'string'],
            'title_en'   => ['required', 'string'],
            'phone'      => ['required', 'string'],
            'email'      => ['required', 'email'],
            'wechat'     => ['required', 'string'],
            'skype'      => ['string'],
            'avatar_url' => ['required', 'string'],
        ];
        $this->validate($request, $rules);

        $key = 'email_signature_' . auth('api')->id();
        $data = json_decode(Cache::get($key, '{}'), true);

        $data['name']       = $request->get('name');
        $data['name_en']    = $request->get('name_en');
        $data['title']      = $request->get('title');
        $data['title_en']   = $request->get('title_en');
        $data['phone']      = $request->get('phone');
        $data['email']      = $request->get('email');
        $data['wechat']     = $request->get('wechat');
        $data['skype']      = $request->get('skype', '');
        $data['avatar_url'] = $request->get('avatar_url');

        Redis::connection()->set($key, json_encode($data));

        return $this->jsonResponse();
    }
}
