<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;
use RuntimeException;
use Illuminate\Support\Facades\Log;

use App\Helpers\Channel;

class ChannelHostAccessAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        $role = Channel::getRoleId(Auth::id());
        $host = $request->getHost();
        $roleHostMap = [
            Channel::CHANNEL_TOPON_ADMIN     => env('TOPON_HOST'),
            Channel::CHANNEL_TOPON_OPERATION => env('TOPON_HOST'),
            Channel::CHANNEL_233             => env('CHANNEL_233_HOST'),
            Channel::CHANNEL_DLADS           => env('CHANNEL_DLADS_HOST'),
        ];
        if ($role === null) {
            throw new RuntimeException("Fatal: The user doesn't assign to any role!");
        }
        if($roleHostMap[$role] !== $host){
            Auth::guard('web')->logout();
            // 退出时清除所有session数据
            $request->session()->flush();
            return redirect('');
        }
        return $next($request);
    }
}
