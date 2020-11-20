<?php

namespace App\Http\Controllers;

use App\Helpers\ArrayUtil;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\SdkVersion;
use App\Models\MySql\Users;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SdkVersionController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('sdk-manage@index');

        $query = SdkVersion::query()
            ->select(['type', 'version', 'status', 'admin_id', 'update_time', 'id'])
            ->where('parent_id', 0);

        if ($request->has('version')) {
            $query->where('version', $request->query('version'));
        }
        if (array_key_exists($request->query('type', -1), SdkVersion::getTypeMap())) {
            $query->where('type', $request->query('type'));
        }
        if (array_key_exists($request->query('status', -1), SdkVersion::getStatusMap())) {
            $query->where('status', $request->query('status'));
        }

        $paginator = $query->orderByDesc('update_time')
            ->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $key => $datum) {
            $data['list'][$key]['admin_name'] = Users::getName($datum['admin_id']);
        }

        return $this->jsonResponse($data);
    }

    public function meta(): JsonResponse
    {
        /* 获取所有 SDK 版本，仅需要 type 与 version 字段 */
        $data = SdkVersion::query()->where('type', '!=', 0)
            ->where('status', SdkVersion::STATUS_ACTIVE)
            ->get(['type', 'version'])->toArray();

        $resMap = [
            SdkVersion::TYPE_AND           => 'original_android_list',
            SdkVersion::TYPE_IOS           => 'original_ios_list',
            SdkVersion::TYPE_UNITY_AND     => 'unity_android_list',
            SdkVersion::TYPE_UNITY_IOS     => 'unity_ios_list',
            SdkVersion::TYPE_UNITY_AND_IOS => 'unity_android_ios_list',
        ];

        $typeMap = SdkVersion::getTypeMap();
        unset($typeMap[SdkVersion::TYPE_NW_FIRM]);
        $res = [
            'original_android_list' => [],
            'original_ios_list' => [],
            'unity_android_list' => [],
            'unity_ios_list' => [],
            'unity_android_ios_list' => [],
        ];
        foreach ($typeMap as $type => $name) {
            $currentTypeData = array_where($data, static function ($value) use ($type) {
                    return $value['type'] == $type;
            });
            foreach ($currentTypeData as $currentTypeDatum) {
                $res[$resMap[$type]][] = $currentTypeDatum['version'];
            }
            $res[$resMap[$type]] = ArrayUtil::sortStrArrWithSegments($res[$resMap[$type]], '.', 3, false);
        }
        return $this->jsonResponse($res);
    }

    /**
     * 新增
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('sdk-manage@store');

        $rules = [
            'type'         => ['required', Rule::in(array_keys(SdkVersion::getTypeMap()))],
            'version'      => ['required', 'string'],
            'update_date'  => ['required', 'date'],
            'size_zh'      => ['required', 'string'],
            'size_en'      => ['required', 'string'],
            'log_url_zh'   => ['required'],
            'log_url_en'   => ['required'],
            'demo_url_zh'  => ['required'],
            'demo_url_en'  => ['required'],
            'status'       => ['required', Rule::in(array_keys(SdkVersion::getStatusMap()))],
            'network_list' => ['required_unless:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'array'],
            'network_list.*.id'        => ['required', Rule::in(array_keys(NetworkFirm::getNwFirmMap()))],
            'network_list.*.version'   => ['required', 'string'],
            'network_list.*.area'      => ['required', Rule::in(array_keys(SdkVersion::getAreaMap()))],
            'network_list.*.formats'   => ['required', 'array'],
            'network_list.*.formats.*' => [Rule::in(array_keys(SdkVersion::getFormatMap()))],
            'unity_android_version' => ['required_if:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'string'],
            'unity_ios_version'     => ['required_if:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'string'],
        ];
        $this->validate($request, $rules);
        $this->validateVersion($request);
        $this->validateIos($request);

        if ($request->get('type') == SdkVersion::TYPE_UNITY_AND_IOS) {
            $this->validateUnity($request);
        }

        try {
            DB::beginTransaction();

            /* 保存父级数据 */
            $record = SdkVersion::query()->updateOrCreate([
                'type'    => $request->get('type'),
                'version' => $request->get('version'),
            ], [
                'update_date' => $request->get('update_date'),
                'size_zh'     => $request->get('size_zh'),
                'size_en'     => $request->get('size_en'),
                'log_url_zh'  => $request->get('log_url_zh'),
                'log_url_en'  => $request->get('log_url_en'),
                'demo_url_zh' => $request->get('demo_url_zh'),
                'demo_url_en' => $request->get('demo_url_en'),
                'status'      => $request->get('status'),
                'admin_id'    => auth()->id()
            ]);

            if ($request->get('type') == SdkVersion::TYPE_UNITY_AND_IOS) {
                $record['extra'] = json_encode([
                    'unity_ios_version' => $request->get('unity_ios_version'),
                    'unity_android_version' => $request->get('unity_android_version')
                ]);
                $record->save();
            } else {
                /* 创建子级数据 */
                $networkList = $request->get('network_list');
                foreach ($networkList as $network) {
                    SdkVersion::query()->updateOrCreate([
                        'parent_id'  => $record['id'],
                        'nw_firm_id' => $network['id'],
                        'area'     => $network['area'],
                    ], [
                        'type'       => SdkVersion::TYPE_NW_FIRM,
                        'version'  => $network['version'],
                        'formats'  => json_encode($network['formats']),
                        'status'   => SdkVersion::STATUS_ACTIVE,
                        'admin_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }
        return $this->jsonResponse([], 1);
    }

    /**
     * 单个信息
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $this->checkAccessPermission('sdk-manage@index');

        $data = SdkVersion::query()->where('id', $id)->where('parent_id', 0)
            ->firstOrFail([
                'id', 'type', 'version', 'update_date', 'size_zh', 'size_en',
                'log_url_zh', 'log_url_en', 'demo_url_zh', 'demo_url_en', 'status', 'extra'
            ])->toArray();

        /* 找子级厂商 SDK */
        $children = SdkVersion::query()->where('parent_id', $id)->where('status', SdkVersion::STATUS_ACTIVE)->get()->toArray();

        foreach ($children as $child) {
            $data['network_list'][] = [
                'id'      => $child['nw_firm_id'],
                'area'    => $child['area'],
                'version' => $child['version'],
                'formats' => json_decode($child['formats'], true)
            ];
        }

        if (!empty($data['extra'])) {
            $extra = json_decode($data['extra'], true);
            foreach ($extra as $key => $value) {
                $data[$key] = $value;
            }
        }
        unset($data['extra']);

        return $this->jsonResponse($data);
    }

    /**
     * 编辑
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('sdk-manage@update');

        $data = SdkVersion::query()->where('id', $id)->where('parent_id', 0)->firstOrFail();

        $rules = [
            'type'         => ['required'],
            'version'      => ['required', 'string'],
            'update_date'  => ['required', 'date'],
            'size_zh'      => ['required', 'string'],
            'size_en'      => ['required', 'string'],
            'log_url_zh'   => ['required'],
            'log_url_en'   => ['required'],
            'demo_url_zh'  => ['required'],
            'demo_url_en'  => ['required'],
            'status'       => ['required', Rule::in(array_keys(SdkVersion::getStatusMap()))],
            'network_list' => ['required_unless:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'array'],
            'network_list.*.id'        => ['required', Rule::in(array_keys(NetworkFirm::getNwFirmMap()))],
            'network_list.*.version'   => ['required', 'string'],
            'network_list.*.area'      => ['required', Rule::in(array_keys(SdkVersion::getAreaMap()))],
            'network_list.*.formats'   => ['required', 'array'],
            'network_list.*.formats.*' => [Rule::in(array_keys(SdkVersion::getFormatMap()))],
            'unity_android_version' => ['required_if:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'string'],
            'unity_ios_version'     => ['required_if:type,' . SdkVersion::TYPE_UNITY_AND_IOS, 'string'],
        ];

        $this->validate($request, $rules);
        $this->validateVersion($request, $id);
        $this->validateIos($request);

        if ($data['type'] == SdkVersion::TYPE_UNITY_AND_IOS) {
            $this->validateUnity($request);
        }

        try {
            DB::beginTransaction();

            /* 更新主体 */
            $data->update([
                'version'     => $request->get('version'),
                'update_date' => $request->get('update_date'),
                'size_zh'     => $request->get('size_zh'),
                'size_en'     => $request->get('size_en'),
                'log_url_zh'  => $request->get('log_url_zh'),
                'log_url_en'  => $request->get('log_url_en'),
                'demo_url_zh' => $request->get('demo_url_zh'),
                'demo_url_en' => $request->get('demo_url_en'),
                'status'      => $request->get('status'),
                'admin_id'    => auth()->id(),
                'update_time' => date('Y-m-d H:i:s')
            ]);

            if ($data['type'] == SdkVersion::TYPE_UNITY_AND_IOS) {
                $data['extra'] = json_encode([
                    'unity_ios_version' => $request->get('unity_ios_version'),
                    'unity_android_version' => $request->get('unity_android_version')
                ]);
                $data->save();
            } else {
                /* 对每个需要更新的 network 进行更新，对于删除的进行状态更改 */
                $networkList = $request->get('network_list');
                $childrenIdList = [];
                foreach ($networkList as $network) {
                    $model = SdkVersion::query()->updateOrCreate([
                        'parent_id'  => $data['id'],
                        'nw_firm_id' => $network['id'],
                        'area'       => $network['area'],
                    ], [
                        'version'  => $network['version'],
                        'formats'  => json_encode($network['formats']),
                        'admin_id' => auth()->id(),
                    ]);
                    $childrenIdList[] = $model['id'];
                }
                SdkVersion::query()->where('parent_id', $data['id'])->whereNotIn('id', $childrenIdList)->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }

        return $this->jsonResponse();
    }

    /**
     * Version 校验
     *
     * @param Request $request
     * @param $id
     * @throws ValidationException
     */
    private function validateVersion(Request $request, $id = 0)
    {
        $query = SdkVersion::query()
            ->where('type', $request->get('type'))
            ->where('version', $request->get('version'));

        if ($id != 0) {
            $query->where('id', '!=', $id);
        }
        if ($query->exists()) {
            throw new ValidationException(null, $this->jsonResponse([
                'version' => 'Version(' . $request->get('version') . ') in same type has already been created.'
            ], 10000));
        }
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    private function validateUnity(Request $request)
    {
        /* 如果是 Unity 安卓 + iOS 时，验证 unity_android_version 与 unity_ios_version */
        if (!SdkVersion::query()
            ->where('type', SdkVersion::TYPE_UNITY_AND)
            ->where('version', $request->get('unity_android_version'))->exists()) {
            throw new ValidationException(null, $this->jsonResponse([
                'unity_android_version' => 'Version ' . $request->get('unity_android_version') . ' has not been created'
            ], 10000));
        }
        if (!SdkVersion::query()
            ->where('type', SdkVersion::TYPE_UNITY_IOS)
            ->where('version', $request->get('unity_ios_version'))->exists()) {
            throw new ValidationException(null, $this->jsonResponse([
                'unity_ios_version' => 'Version ' . $request->get('unity_ios_version') . ' has not been created'
            ], 10000));
        }
    }

    /**
     * 校验 iOS 时，所有 network 的 area 必须是 other
     *
     * @param Request $request
     * @throws ValidationException
     */
    private function validateIos(Request $request)
    {
        if (in_array($request->get('type'), [SdkVersion::TYPE_IOS, SdkVersion::TYPE_UNITY_IOS], false)) {
            $networks = $request->get('network_list');
            foreach ($networks as $idx => $network) {
                if ($network['area'] == SdkVersion::AREA_NATIVE) {
                    throw new ValidationException(null, $this->jsonResponse([
                        'network.' . $idx . '.area' => "The network's area doesn't support cn when type is iOS"
                    ], 10000));
                }
            }
        }
    }
}
