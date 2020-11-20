<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user === null) {
            throw new HttpException("Cannot find logged in user.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (method_exists($user, 'hasPermissionTo') && !$user->hasPermissionTo('Administer roles & permissions')) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Permission deny");
        }
        return $next($request);
    }
}
