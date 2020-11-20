<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\User;
use Closure;
use Illuminate\Http\Request;
use Log;

class CheckUserStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        assert($user instanceof User);

        Log::info('test');

        if ($user['status'] != User::STATUS_RUNNING) {
            /* 退出这个依旧生效的 token 或者 session */
            if ($request->expectsJson() || strpos($request->getUri(), '/api')) {
                auth('api')->logout();
                return (new ApiController())->jsonResponse([], 9002);
            }
            auth('web')->logout();
            return redirect('/');
        }
        return $next($request);
    }
}
