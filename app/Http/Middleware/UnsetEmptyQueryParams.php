<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 把请求的 Query 中的空参数去除掉，不会去除掉 Body 中的空参数
 *
 * Class UnsetEmptyQueryParams
 * @package App\Http\Middleware
 */
class UnsetEmptyQueryParams
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
        $query = $request->query();

        foreach ($query as $key => $value) {
            if ($value === '' || $value === [] || $value === [""]) {
                $request->query->remove($key);
            }
        }

        return $next($request);
    }
}
