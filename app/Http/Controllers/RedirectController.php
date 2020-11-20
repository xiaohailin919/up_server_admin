<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class RedirectController extends BaseController
{

    public function redirect(Request $request) {
        $destUrl = $request->get('dest');
        $key = 'admin_' . auth('web')->id();
        $ticket = auth('web')->id() . '_' . time();
        Redis::command('setex', [$key, 3600 * 24 * 14, $ticket]);

        return redirect('http://' . env('TOPON_HOST') . '/t/auth-redirect?ticket=' . base64_encode($ticket) . '&targetPath=' .  $destUrl);
    }
}
