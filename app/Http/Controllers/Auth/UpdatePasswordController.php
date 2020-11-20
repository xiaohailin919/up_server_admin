<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UpdatePasswordController extends Controller
{
    use RedirectsUsers;

    /**
     * 用户更新密码后重定向的地址
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * 原密码错误
     */
    const INVALID_OLD_PASSWORD = 'passwords.old_password';

    /**
     * 用户异常
     */
    const INVALID_USER = 'passwords.user_invalid';

    /**
     * 用户密码异常
     */
    const INVALID_USER_PASSWORD = 'passwords.user_password';

    /**
     * 更新密码成功
     */
    const PASSWORD_UPDATE = 'passwords.update';

    /**
     * 认证后用户可访问
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 显示更新密码列表
     *
     * @return Factory|View
     */
    public function showUpdateForm() {
        return view('auth.passwords.update');
    }

    /**
     * 更新用户密码
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        /* 表单验证 */
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        /* 数据凭证验证 */
        $response = $this->validateAndUpdate($request);

        return $response === static::PASSWORD_UPDATE
            ? $this->sendUpdateResponse()
            : $this->sendUpdateFailedResponse($request, $response);

    }

    /**
     * 表单验证规则，不涉及数据库数据的验证
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'old_password'          => 'required|min:6',
            'password'              => 'required|min:6|confirmed|different:old_password',
            'password_confirmation' => 'required|min:6',
        ];
    }

    /**
     * 表单验证规则错误信息，使用默认的即可。
     *
     * @return array
     */
    protected function validationErrorMessages(): array
    {
        return [];
    }

    /**
     * 验证并更新，此操作按照规范应在 broker 中完成
     * 但 laravel 寻找 broker 的反射功能容易出错 ←_←
     *
     * @param Request $request
     * @return User|string|null
     */
    protected function validateAndUpdate(Request $request) {

        $credentials = $this->credentials($request);
        $validateRes = $this->validateCredentials($credentials);

        /* 若无法获取 User 对象，则直接向上抛出认证失败信息 */
        if (! $validateRes instanceof User) {
            return $validateRes;
        }

        $password = $credentials['password'];
        $this->updatePassword($validateRes, $password);
        return static::PASSWORD_UPDATE;
    }

    /**
     * 从请求体中获取认证凭证
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        return $request->only('old_password', 'password', 'password_confirmation');
    }

    /**
     * 根据获取的凭证进行认证，若认证成功返回当前用户对象
     *
     * @param array $credentials
     * @return User|string|null
     */
    protected function validateCredentials(array $credentials) {
        $user = Auth::user();
        $password = $credentials['old_password'];
        if (!isset($user)) {
            return static::INVALID_USER;
        }
        if (!isset($user->password)) {
            return static::INVALID_USER_PASSWORD;
        }
        if (!Hash::check($password, $user->password)) {
            return static::INVALID_OLD_PASSWORD;
        }
        return $user;
    }

    /**
     * 更新密码逻辑
     * User 中定义了一个设置器，在持久化数据之前会对 password 字段进行 bcrypt 加密
     *
     * @param User $user
     * @param string $password
     */
    protected function updatePassword(User $user, string $password) {
        $user->password = $password;
        $user->save();
        Auth::logout();
    }


    /**
     * 认证成功返回请求体
     * @return RedirectResponse
     */
    protected function sendUpdateResponse(): RedirectResponse
    {
        return redirect($this->redirectPath());
    }

    /**
     * 认证失败返回请求体
     * @param Request $request
     * @param $response
     * @return RedirectResponse
     */
    protected function sendUpdateFailedResponse(Request $request, $response): RedirectResponse
    {
        return redirect()->back()
            ->withInput($request->only('old_password'))
            ->withErrors(['old_password' => trans($response)]);
    }
}
