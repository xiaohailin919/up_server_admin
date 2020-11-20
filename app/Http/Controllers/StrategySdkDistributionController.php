<?php


namespace App\Http\Controllers;


use App\Helpers\ArrayUtil;
use App\Models\MySql\Publisher;
use App\Models\MySql\PublisherGroup;
use App\Models\MySql\SdkVersion;
use App\Models\MySql\StrategySdkDistribution;
use App\Models\MySql\Users;
use App\Services\UserService;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StrategySdkDistributionController extends ApiController
{
    private $metricMap = [
        SdkVersion::TYPE_AND           => ['original_android_list' , 'android'],
        SdkVersion::TYPE_IOS           => ['original_ios_list'     , 'ios'],
        SdkVersion::TYPE_UNITY_AND     => ['unity_android_list'    , 'unity_android'],
        SdkVersion::TYPE_UNITY_IOS     => ['unity_ios_list'        , 'unity_ios'],
        SdkVersion::TYPE_UNITY_AND_IOS => ['unity_android_ios_list', 'unity_android_ios'],
    ];

    public function index(Request $request)
    {
        $this->checkAccessPermission('strategy-sdk-distribution@index');

        /* 搜索条件包括开发者名称，必须联 Publisher */
        $query = StrategySdkDistribution::query()
            ->from('strategy_sdk_distribution as t1')
            ->select(['t1.*'])
            ->selectRaw("IFNULL(t2.name, '') as publisher_name")
            ->leftJoin('publisher as t2', 't2.id', '=', 't1.publisher_id')
            ->where('t1.status', StrategySdkDistribution::STATUS_ACTIVE)
            ->orderBy('t1.type')
            ->orderByDesc('t1.update_time');

        if ($request->has('publisher_id')) {
            $query->where('t2.id', $request->query('publisher_id'));
        }
        if ($request->has('publisher_name')) {
            $query->where('t2.name', 'like' , '%' . $request->query('publisher_name') . '%');
        }
        if (array_key_exists($request->query('publisher_group_id', -1), PublisherGroup::getPublisherGroupIdNameMap())) {
            $query->where('t1.publisher_group_id', $request->query('publisher_group_id'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        $operablePublisherIdList = UserService::getPublisherIdListByUserId(auth()->id());

        foreach ($data['list'] as $i => $datum) {
            $data['list'][$i]['publisher_group_name'] = PublisherGroup::getName($datum['publisher_group_id']);
            $data['list'][$i]['admin_name'] = Users::getName($datum['admin_id']);
            $data['list'][$i]['operable'] = in_array($datum['publisher_id'], $operablePublisherIdList, false) ? 1 : 2;
            foreach ($this->metricMap as $type => $metric) {
                $versionList = json_decode($datum[$metric[1]], true);

                /* 将列表中已删除的版本剔除 */
                foreach ($versionList as $key => $version) {
                    if (!SdkVersion::isActive($version, $type)) {
                        unset($versionList[$key]);
                    }
                }

                $versionList = ArrayUtil::sortStrArrWithSegments($versionList, '.', 3, false);
                $data['list'][$i][$metric[0]] = $versionList;
                unset($data['list'][$i][$metric[1]]);
            }
        }

        return $this->jsonResponse($data);
    }

    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('strategy-sdk-distribution@store');

        $rules = [
            'type'                     => ['required', Rule::in(array_keys(StrategySdkDistribution::getTypeMap()))],
            'publisher_id_list'        => ['required_if:type,1', 'array'],
            'publisher_id_list.*'      => ['exists:publisher,id'],
            'publisher_group_id_list'  => ['required_if:type,2', 'array'],
            'publisher_group_id_list.*'=> ['exists:publisher_group,id'],
            'original_android_list'    => ['required', 'array'],
            'original_android_list.*'  => ['string', 'distinct'],
            'original_ios_list'        => ['required', 'array'],
            'original_ios_list.*'      => ['string', 'distinct'],
            'unity_android_list'       => ['required', 'array'],
            'unity_android_list.*'     => ['string', 'distinct'],
            'unity_ios_list'           => ['required', 'array'],
            'unity_ios_list.*'         => ['string', 'distinct'],
            'unity_android_ios_list'   => ['required', 'array'],
            'unity_android_ios_list.*' => ['string', 'distinct'],
        ];

        $this->validate($request, $rules);

        $type = $request->input('type');
        $publisherIdList = $type == StrategySdkDistribution::TYPE_PUBLISHER ? $request->input('publisher_id_list') : [0];
        $publisherGroupIdList = $type == StrategySdkDistribution::TYPE_GROUP ? $request->input('publisher_group_id_list') : [0];
        $adminId = auth()->id();

        try {
            DB::beginTransaction();

            foreach ($publisherIdList as $publisherId) {
                foreach ($publisherGroupIdList as $groupId) {
                    StrategySdkDistribution::query()->updateOrInsert([
                        'type'               => $type,
                        'publisher_id'       => $publisherId,
                        'publisher_group_id' => $groupId,
                    ], [
                        'android'           => json_encode($request->input('original_android_list')),
                        'ios'               => json_encode($request->input('original_ios_list')),
                        'unity_android'     => json_encode($request->input('unity_android_list')),
                        'unity_ios'         => json_encode($request->input('unity_ios_list')),
                        'unity_android_ios' => json_encode($request->input('unity_android_ios_list')),
                        'admin_id'          => $adminId,
                        'status'            => StrategySdkDistribution::STATUS_ACTIVE,
                        'create_time'       => date('Y-m-d H:i:s'),
                        'update_time'       => date('Y-m-d H:i:s')
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([
                'msg'   => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTrace()
            ], 9995);
        }
        return $this->jsonResponse([], 1);
    }

    public function show($id)
    {
        $this->checkAccessPermission('strategy-sdk-distribution@index');

        $strategy = StrategySdkDistribution::query()->find($id);

        if ($strategy == null) {
            return $this->jsonResponse([], 10003);
        }

        $strategy['publisher_id']         = (string)$strategy['publisher_id'];
        $strategy['publisher_name']       = Publisher::getName($strategy['publisher_id']);
        $strategy['publisher_group_name'] = PublisherGroup::getName($strategy['publisher_group_id']);

        foreach ($this->metricMap as $type => $metric) {
            $tmp = json_decode($strategy[$metric[1]], true);
            foreach ($tmp as $key => $value) {
                if (!SdkVersion::isActive($value, $type)) {
                    unset($tmp[$key]);
                }
            }
            $strategy[$metric[0]] = array_values($tmp);
        }

        unset($strategy['android'], $strategy['ios'], $strategy['unity_android'], $strategy['unity_android_ios'], $strategy['unity_android_ios']);

        return $this->jsonResponse($strategy);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('strategy-sdk-distribution@update');

        $rules = [
            'original_android_list'    => ['required', 'array'],
            'original_android_list.*'  => ['string', 'distinct'],
            'original_ios_list'        => ['required', 'array'],
            'original_ios_list.*'      => ['string', 'distinct'],
            'unity_android_list'       => ['required', 'array'],
            'unity_android_list.*'     => ['string', 'distinct'],
            'unity_ios_list'           => ['required', 'array'],
            'unity_ios_list.*'         => ['string', 'distinct'],
            'unity_android_ios_list'   => ['required', 'array'],
            'unity_android_ios_list.*' => ['string', 'distinct'],
        ];
        $this->validate($request, $rules);

        StrategySdkDistribution::query()->where('id', $id)
            ->update([
                'android'           => json_encode($request->input('original_android_list')),
                'ios'               => json_encode($request->input('original_ios_list')),
                'unity_android'     => json_encode($request->input('unity_android_list')),
                'unity_ios'         => json_encode($request->input('unity_ios_list')),
                'unity_android_ios' => json_encode($request->input('unity_android_ios_list')),
                'admin_id'          => auth('api')->id(),
                'update_time'       => date('Y-m-d H:i:s'),
            ]);

        return $this->jsonResponse();
    }

    public function destroy($id){
        $this->checkAccessPermission('strategy-sdk-distribution@destroy');

        StrategySdkDistribution::query()->where('id', $id)
            ->update([
                'status' => StrategySdkDistribution::STATUS_STOP,
                'admin_id' => auth('api')->id(),
                'update_time' => date('Y-m-d H:i:s'),
            ]);

        return $this->jsonResponse();
    }
}