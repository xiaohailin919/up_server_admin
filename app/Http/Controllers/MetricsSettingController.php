<?php
/**
 * Created by PhpStorm.
 * User: Liu
 * Date: 2020/2/6
 * Time: 9:43 AM
 */

namespace App\Http\Controllers;

use App\Models\MySql\MetricsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetricsSettingController extends BaseController{
    public function fullReport(Request $request){
        $metricsIds = $request->input('metrics_setting');
        $kind       = 7;

        MetricsSetting::where('kind', $kind)
            ->where('publisher_id', 0)
            ->where('admin_id', Auth::id())
            ->delete();

        foreach($metricsIds as $val){
            $date = date('Y-m-d H:i:s');
            $tmp  = [
                'kind'         => 7,
                'metrics_id'   => $val,
                'publisher_id' => 0,
                'admin_id'     => Auth::id(),
                'create_time'  => $date,
            ];
            $insertData[] = $tmp;
        }

        if(empty($insertData)){
            return redirect()->back();
        }

        $result = MetricsSetting::insert($insertData);
        return redirect()->back();
    }
}