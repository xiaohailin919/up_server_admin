<?php

namespace App\Http\Controllers;

use App\User;
use Hash;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login', 'ticketLogin']]);
    }

    /**
     * 通过账号密码登陆的方式进行验证获取 token
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);
        Log::info($credentials);

        /* 必须未被删除的 */
        $credentials['status'] = User::STATUS_RUNNING;

        if (! $token = auth('api')->attempt($credentials)) {
            return new JsonResponse([
                'code' => 9005,
                'msg'  => __('code.9005'),
                'data' => [],
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $this->respondWithToken($token);
    }

    /**
     * 通过票据获取 token 接口
     *
     * @return JsonResponse
     */
    public function ticketLogin(): JsonResponse
    {
        /* 验证票据是否有效 */
        $ticket    = request('ticket', '');
        $certParse = base64_decode($ticket);
        $id        = substr($certParse, 0, strpos($certParse, '_'));
        $certStore = Redis::connection()->command('get', ['admin_' . $id]);
        /* 校验失败 */
        if ($ticket == '' || $certStore == '' || $certParse != $certStore) {
            return new JsonResponse([
                'code' => 9005,
                'msg'  => __('code.9005'),
                'data' => ['ticket' => 'ticket error'],
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        /* 匹配成功，删除该 key */
        Redis::connection()->command('del', ['admin_' . $id]);

        $user = User::query()->find($id);

        if (empty($user) || get_class($user) != User::class) {
            return new JsonResponse([
                'code' => 9005,
                'msg'  => __('code.9005'),
                'data' => [],
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $token = auth('api')->login($user);
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user == null || get_class($user) != User::class) {
            return new JsonResponse([
                'code' => 9005,
                'msg'  => __('code.9005'),
                'data' => [],
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        /* 获取用户信息 */
        $permissions = DB::select('select t1.name, t1.id, t4.id as role_id, t4.name as role_name from permissions t1
            left join role_has_permissions t2 on t1.id = t2.permission_id
            left join model_has_roles t3 on t2.role_id = t3.role_id
            left join roles t4 on t4.id = t3.role_id
            where t3.model_id = ?;', [$user->id]);

        /* 调整格式 */
        $res['id'] = $user['id'];
        $res['name'] = $user['name'];
        $res['email'] = $user['email'];
        $res['permission_list'] = [];
        foreach ($permissions as $permission) {
            if (!isset($res['role_id'])) {
                $res['role_id'] = $permission['role_id'];
                $res['role_name'] = $permission['role_name'];
            }
            $res['permission_list'][] = ['id' => $permission['id'], 'name' => $permission['name']];
        }

        return new JsonResponse([
            'code' => 0,
            'msg'  => 'Get personal info success',
            'data' => $res
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return new JsonResponse([
            'code' => 0,
            'msg'  => 'Successfully logged out',
            'data' => []
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return new JsonResponse([
            'code' => 0,
            'msg'  => 'Login success',
            'data' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60
            ]
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard(): Guard
    {
        return auth();
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $rules = [
            'old_password'          => ['required', 'string', 'min:6'],
            'password'              => ['required', 'confirmed', 'string', 'min:6', 'different:old_password'],
            'password_confirmation' => ['required'],
        ];

        $this->validate($request, $rules);


        $user = auth('api')->user();
        assert($user instanceof User);

        $oldPassword = $request->get('old_password');

        if (!Hash::check($oldPassword, $user['password'])) {
            return new JsonResponse([
                'code' => 10000,
                'msg'  => 'Incorrect password.',
                'data' => ['old_password' => 'Incorrect password.']
            ], 422, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $user->setPasswordAttribute($request->get('password'));
        $user->save();

        auth('api')->logout();

        return new JsonResponse([
            'code' => 0,
            'msg'  => 'Update password success, please log in.',
            'data' => [],
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
