<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnableCORSMiddleware
{
    /**
     * CORS 冲突解决中间件
     * 注：OPTIONS 请求除了全局路由会走之外，不会走到其他路由组，因此其他路由组所定义的中间件对它无效
     *
     * @param Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ?: '';
        $allow_origin = [
            'http://localhost',
            'http://127.0.0.1',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:4000',
            'http://192.168.10.10:8080',
        ];
        if (in_array($origin, $allow_origin, true)) {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, User-Agent, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
            $response->header('Access-Control-Allow-Credentials', 'true');
        } else if ($origin != '') {
            Log::info('Request block by cors: ' . $request->getMethod() . ', ' . $request->getBaseUrl() . ', ' . $request->getHost() . ', origin: ' . $origin);
        }
        return $response;
    }
}
