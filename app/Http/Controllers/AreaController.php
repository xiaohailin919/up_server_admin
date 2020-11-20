<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MySql\Geo;

use App\Helpers\ArrayUtil;

class AreaController extends ApiController
{
    /**
     * 地区信息列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $geo = Geo::select('code_id as code', 'short as value', 'name as label', 'continent', 'sort')
            ->where('id', '<', 272)
            ->where('code_id', '>', 0)
            ->get();

        foreach($geo as &$val){
            $val['label'] = __('geo.' . $val['value']);
        }

        return $this->jsonResponse($geo);
    }

    /**
     * 地区，按洲分组
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function grouping(Request $request){
        $geo = Geo::select('code_id as code', 'short as value', 'name as label', 'continent', 'sort')
            ->where('id', '<', 272)
            ->where('code_id', '>', 0)
            ->get();

        // 匹配语言
        foreach($geo as &$v){
            $v['label'] = __('geo.' . $v['value']);
        }

        $geo = ArrayUtil::groupBy($geo, 'continent');

        $response = [];

//        // Top
//        $topGeo = Geo::select('code_id as code', 'short as value', 'name as label', 'continent', 'sort')
//            ->whereIn('short', ['CN', 'JP', 'KR', 'US', 'UK'])
//            ->orderBy('short')
//            ->get()
//            ->toArray();
//        foreach($topGeo as &$v){
//            $v['label'] = __('geo.' . $v['value']);
//        }
//        $response[] = [
//            'value' => 'TOP',
//            'label' => __('continent.TOP'),
//            'children' => $topGeo
//        ];

        foreach($geo as $key => $val){
            $tmp = [
                'value' => $key,
                'label' => __('continent.' . $key),
                'children' => $val,
            ];
            $response[] = $tmp;
        }

        return $this->jsonResponse($response);
    }
}
