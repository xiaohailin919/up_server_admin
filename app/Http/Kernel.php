<?php

namespace App\Http;

use App\Http\Middleware\CheckAdminMiddleware;
use App\Http\Middleware\CheckUserStatusMiddleware;
use App\Http\Middleware\UnsetEmptyQueryParams;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\ChannelHostAccessAuthenticate;
use App\Http\Middleware\EnableCORSMiddleware;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        TrustProxies::class,
        EnableCORSMiddleware::class,
        UnsetEmptyQueryParams::class
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,                // Cookie 加密中间件
            AddQueuedCookiesToResponse::class,    // Cookie 队列中间件
            StartSession::class,                  // Session 创建中间件，独属于 web，因为必须有登录态
            ShareErrorsFromSession::class,        //
            ConvertEmptyStringsToNull::class,     // 将 Input 里面的空值转成 null，我也不知道框架为什么要搞这个东西 >_<
            VerifyCsrfToken::class,               // Csrf token 验证中间件
            SubstituteBindings::class,            // 参数/模型隐式转化中间件
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                => Authenticate::class,
        'auth.basic'          => AuthenticateWithBasicAuth::class,
        'bindings'            => SubstituteBindings::class,             // 参数/模型隐式转化中间件
        'can'                 => Authorize::class,
        'guest'               => RedirectIfAuthenticated::class,
        'throttle'            => ThrottleRequests::class,               // 接口访问次数限制中间件
        'channel_host_access' => ChannelHostAccessAuthenticate::class,  // 限制渠道访问域名
        'cors'                => EnableCORSMiddleware::class,           // 跨域放行
        'admin'               => CheckAdminMiddleware::class,           // 管理员校验
        'user.status'         => CheckUserStatusMiddleware::class       // 用户状态校验，判断是否被删除
    ];

    /**
     * 非全局中间件执行顺序
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        EncryptCookies::class,
        StartSession::class,
        Authenticate::class,
        \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        CheckUserStatusMiddleware::class,
        CheckAdminMiddleware::class,
    ];
}
