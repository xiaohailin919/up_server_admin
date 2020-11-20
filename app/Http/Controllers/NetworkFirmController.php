<?php

namespace App\Http\Controllers;

use App\Models\MySql\FirmAdapter;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NetworkFirmController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('network-firm@index');

        $query = NetworkFirm::query()
            ->where('api_version', NetworkFirm::SYSTEM_UP);
        if ($request->has('id')) {
            $query->where('id', $request->query('id'));
        }
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->query('publisher_id', -1) > -1) {
            $query->where('publisher_id', $request->query('publisher_id'));
        }
        /* 接口 【1：否，2：是】，数据库【0：否，1：是】 */
        if (in_array($request->query('format_native', -1), [1, 2], false)) {
            $query->where('native', $request->query('format_native') - 1);
        }
        if (in_array($request->query('format_rv', -1), [1, 2], false)) {
            $query->where('rewarded_video', $request->query('format_rv') - 1);
        }
        if (in_array($request->query('format_banner', -1), [1, 2], false)) {
            $query->where('banner', $request->query('format_banner') - 1);
        }
        if (in_array($request->query('format_iv', -1), [1, 2], false)) {
            $query->where('interstitial', $request->query('format_iv') - 1);
        }
        if (in_array($request->query('format_splash', -1), [1, 2], false)) {
            $query->where('splash', $request->query('format_splash') - 1);
        }
        if (in_array($request->query('crawl_day', -1), [1, 2], false)) {
            $query->where('crawl_support', $request->query('crawl_day') - 1);
        }
        /* 接口 【1：否，2：是】，数据库 【1：否，2：是】 */
        if (in_array($request->query('crawl_hour', -1), [1, 2], false)) {
            $query->where('crawl_support_hour', $request->query('crawl_hour'));
        }
        if (in_array($request->query('currency', '-1'), NetworkFirm::getCurrencyList(), true)) {
            $query->where('report_currency', $request->query('currency'));
        }
        if ($request->has('order_by_field_list') && $request->has('order_by_direction_list')) {
            $orderByFields = $request->query('order_by_field_list');
            $orderByDirections = $request->query('order_by_direction_list');
            foreach ($orderByFields as $idx => $orderByField) {
                if (in_array($orderByField, ['id', 'name', 'publisher_id', 'rank', 'create_time', 'update_time'], true)) {
                    $query->orderBy($orderByField, $orderByDirections[$idx]);
                }
            }
        }
        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        $operablePublisherIdList = UserService::getPublisherIdListByUserId(auth()->id());
        $formatMetricMap = [
            NetworkFirm::FORMAT_NATIVE => 'native',
            NetworkFirm::FORMAT_RV => 'rewarded_video',
            NetworkFirm::FORMAT_BANNER => 'banner',
            NetworkFirm::FORMAT_INTERSTITIAL => 'interstitial',
            NetworkFirm::FORMAT_SPLASH => 'splash',
        ];
        foreach ($data['list'] as $idx => $datum) {
            $data['list'][$idx]['publisher_name'] = Publisher::getName($datum['publisher_id']);
            $data['list'][$idx]['operable'] = in_array($datum['publisher_id'], $operablePublisherIdList, false) ? 1 : 2;
            foreach ($formatMetricMap as $format => $formatMetric) {
                $data['list'][$idx]['format_list'][$format] = [
                    'android' => FirmAdapter::getNameByComplicated($datum['id'], $format, NetworkFirm::PLATFORM_ANDROID),
                    'ios'     => FirmAdapter::getNameByComplicated($datum['id'], $format, NetworkFirm::PLATFORM_IOS),
                    'active'  => $datum[$formatMetric] == 1 ? 2 : 1,
                ];
                unset($data['list'][$idx][$formatMetric]);
            }
            $data['list'][$idx]['crawl_day'] = $datum['crawl_support'] == 1 ? 2 : 1;
            $data['list'][$idx]['crawl_hour'] = $datum['crawl_support_hour'];
            $data['list'][$idx]['currency'] = $datum['report_currency'];
        }

        return $this->jsonResponse($data);
    }

    /**
     * 返回下一个 NetworkFirmID
     *
     * @return JsonResponse
     */
    public function nextNetworkFirmId(): JsonResponse
    {
        $this->checkAccessPermission('network-firm@index');
        $ids = array_column(NetworkFirm::query()->orderBy('id')->get(['id'])->toArray(), 'id');
        /* 见缝插针法 */
        $idx = $ids[0];
        foreach ($ids as $id) {
            if ($idx < $id) {
                return $this->jsonResponse($idx);
            }
            $idx++;
        }
        return $this->jsonResponse($idx);
    }

    public function show($id): JsonResponse
    {
        $this->checkAccessPermission('network-firm@index');

        $nwFirm = NetworkFirm::query()->from(NetworkFirm::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->select([
                't1.id', 't1.name', 't1.type', 't1.publisher_id', 't1.native as format_native',
                't1.rewarded_video as format_rv', 't1.banner as format_banner', 't1.interstitial as format_iv',
                't1.splash as format_splash', 't1.crawl_support as crawl_day', 't1.crawl_support_hour as crawl_hour',
                't1.report_currency as currency', 't1.status', 't1.rank'
            ])->selectRaw("IFNULL(t2.name, 'ALL') as publisher_name")
            ->where('t1.id', $id)
            ->where('t1.id', '<', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->firstOrFail()->toArray();
        $nwFirm['format_native']++;
        $nwFirm['format_rv']++;
        $nwFirm['format_banner']++;
        $nwFirm['format_iv']++;
        $nwFirm['format_splash']++;
        $nwFirm['crawl_day']++;
        return $this->jsonResponse($nwFirm);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('network-firm@store');
        $rules = [
            'id'            => ['required','integer', Rule::notIn(array_keys(NetworkFirm::getAllNwFirmMap()))],
            'name'          => ['required', 'string', Rule::notIn(array_values(NetworkFirm::getAllIntegratedNwFirmMap()))],
            'type'          => ['required', Rule::in([NetworkFirm::TYPE_NORMAL, NetworkFirm::TYPE_ONLINE_API])],
            'format_native' => ['required', 'integer', 'in:1,2'],
            'format_rv'     => ['required', 'integer', 'in:1,2'],
            'format_banner' => ['required', 'integer', 'in:1,2'],
            'format_iv'     => ['required', 'integer', 'in:1,2'],
            'format_splash' => ['required', 'integer', 'in:1,2'],
            'crawl_day'     => ['required', 'integer', 'in:1,2'],
            'crawl_hour'    => ['required', 'integer', 'in:1,2'],
            'currency'      => ['required', 'string', Rule::in(NetworkFirm::getCurrencyList())],
            'rank'          => ['required', 'integer'],
        ];

        $this->validate($request, $rules);

        NetworkFirm::query()->create([
            'id'                 => $request->input('id'),
            'name'               => $request->input('name'),
            'type'               => $request->input('type'),
            'native'             => $request->input('format_native') - 1,
            'rewarded_video'     => $request->input('format_rv') - 1,
            'banner'             => $request->input('format_banner') - 1,
            'interstitial'       => $request->input('format_iv') - 1,
            'splash'             => $request->input('format_splash') - 1,
            'crawl_support'      => $request->input('crawl_day') - 1,
            'crawl_support_hour' => $request->input('crawl_hour'),        // 仅小时表支持是【1：否，2：是】
            'report_currency'    => $request->input('currency'),
            'rank'               => $request->input('rank'),
            'api_version'        => NetworkFirm::SYSTEM_UP,
            'create_time'        => time(),
            'update_time'        => time(),
        ]);

        return $this->jsonResponse([], 1);
    }

    /**
     * 更新
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('network-firm@update');

        $networkFirm = NetworkFirm::query()->where('id', $id)->where('id', '<', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)->firstOrFail();

        $integratedNwFirmMap = NetworkFirm::getAllIntegratedNwFirmMap();
        unset($integratedNwFirmMap[$id]);
        $rules = [
            'name'          => ['required', 'string', Rule::notIn(array_values($integratedNwFirmMap))],
            'type'          => ['required', Rule::in([NetworkFirm::TYPE_NORMAL, NetworkFirm::TYPE_ONLINE_API])],
            'format_native' => ['required', 'integer', 'in:1,2'],
            'format_rv'     => ['required', 'integer', 'in:1,2'],
            'format_banner' => ['required', 'integer', 'in:1,2'],
            'format_iv'     => ['required', 'integer', 'in:1,2'],
            'format_splash' => ['required', 'integer', 'in:1,2'],
            'crawl_day'     => ['required', 'integer', 'in:1,2'],
            'crawl_hour'    => ['required', 'integer', 'in:1,2'],
            'currency'      => ['required', 'string', Rule::in(NetworkFirm::getCurrencyList())],
            'rank'          => ['required', 'integer'],
        ];
        $this->validate($request, $rules);

        $networkFirm->update([
            'name'               => $request->input('name'),
            'type'               => $request->input('type'),
            'native'             => $request->input('format_native') - 1,
            'rewarded_video'     => $request->input('format_rv') - 1,
            'banner'             => $request->input('format_banner') - 1,
            'interstitial'       => $request->input('format_iv') - 1,
            'splash'             => $request->input('format_splash') - 1,
            'crawl_support'      => $request->input('crawl_day') - 1,
            'crawl_support_hour' => $request->input('crawl_hour'),        // 仅小时表支持是【1：否，2：是】
            'report_currency'    => $request->input('currency'),
            'rank'               => $request->input('rank'),
            'update_time'        => time(),
        ]);

        return $this->jsonResponse();
    }

    /**
     * 广告厂商元数据
     * 不包括自定义广告平台、Adx、MyOffer
     *
     * @return JsonResponse
     */
    public function meta(): JsonResponse
    {
        $data = NetworkFirm::query()->select(['id as value', 'name as label'])
            ->where('id', '!=', NetworkFirm::MYOFFER)
            ->where('id', '!=', NetworkFirm::ADX)
            ->where('id', '<=', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->get()->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 广告厂商元数据 - 非自定义广告平台
     *
     * @return JsonResponse
     */
    public function metaNonCustom(): JsonResponse
    {
        $data = NetworkFirm::query()->select(['id as value', 'name as label'])
            ->where('id', '<=', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->get()->toArray();
        return $this->jsonResponse($data);
    }
    
    /**
     * 广告厂商元数据 - 自定义广告平台
     *
     * @return JsonResponse
     */
    public function metaCustom(): JsonResponse
    {
        $data = NetworkFirm::query()
            ->from(NetworkFirm::TABLE. ' as t1')
            ->leftJoin(Publisher::TABLE. ' as t2','t1.publisher_id','=','t2.id')
            ->select(['t1.id as value', 't1.name as label','t1.publisher_id','t2.name as publisher_name'])
            ->where('t1.id', '>', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->get()->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 广告厂商元数据 - 所有广告厂商
     *
     * @return JsonResponse
     */
    public function metaAll(): JsonResponse
    {
        $data = NetworkFirm::query()->select(['id as value', 'name as label'])
            ->get()->toArray();
        return $this->jsonResponse($data);
    }
}
