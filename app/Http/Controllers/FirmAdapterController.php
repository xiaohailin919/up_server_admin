<?php

namespace App\Http\Controllers;

use App\Models\MySql\FirmAdapter;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Services\UserService;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FirmAdapterController extends ApiController
{
    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('firm-adapter@index');

        $query = FirmAdapter::query()
            ->select(['id', 'firm_id as nw_firm_id', 'publisher_id', 'platform', 'format', 'adapter', 'create_time', 'update_time'])
            ->where('system', FirmAdapter::SYSTEM_UP)
            ->orderByDesc('id');

        if ($request->query('nw_firm_id', -1) > -1) {
            $query->where('firm_id', $request->query('nw_firm_id'));
        }
        if ($request->query('publisher_id', -1) > -1) {
            $query->where('publisher_id', $request->query('publisher_id'));
        }
        if (array_key_exists($request->query('platform', -1), FirmAdapter::getPlatformMap())) {
            $query->where('platform', $request->query('platform'));
        }
        if (array_key_exists($request->query('format', -1), FirmAdapter::getFormatMap())) {
            $query->where('format', $request->query('format'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        $operablePublisherIdList = UserService::getPublisherIdListByUserId(auth()->id());
        foreach ($data['list'] as $key => $datum) {
            $data['list'][$key]['nw_firm_name'] = FirmAdapter::getNwFirmName($datum['nw_firm_id']);
            $data['list'][$key]['publisher_name'] = Publisher::getName($datum['publisher_id']);
            $data['list'][$key]['operable'] = in_array($datum['publisher_id'], $operablePublisherIdList, false) ? 1 : 2;
            $data['list'][$key]['create_time'] = $datum['create_time'] * 1000;
            $data['list'][$key]['update_time'] = $datum['update_time'] * 1000;
        }

        return $this->jsonResponse($data);
    }

    /**
     * 单个信息
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $this->checkAccessPermission('firm-adapter@index');

        $adapter = FirmAdapter::query()
            ->from(FirmAdapter::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t3', 't3.id', '=', 't1.firm_id')
            ->selectRaw("t1.id, t1.publisher_id, IFNULL(t2.name, 'ALL') as publisher_name, t1.firm_id as nw_firm_id,
                t3.name as nw_firm_name, t1.format, t1.platform, t1.adapter")
            ->where('t1.id', $id)
            ->firstOrFail();

        return $this->jsonResponse($adapter);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('firm-adapter@store');

        $publisherIdList = Publisher::getAllPublisherId();
        $publisherIdList[] = 0; // 代表所有开发者

        $rules = [
            'nw_firm_id'     => ['required', Rule::in(array_keys(FirmAdapter::getAllIntegratedNwFirmMap()))],
            'publisher_id'   => ['required', Rule::in($publisherIdList)],
            'android'        => ['required', 'array'],
            'ios'            => ['required', 'array'],
            'android.native' => ['present', 'string'],
            'android.rv'     => ['present', 'string'],
            'android.banner' => ['present', 'string'],
            'android.iv'     => ['present', 'string'],
            'android.splash' => ['present', 'string'],
            'ios.native'     => ['present', 'string'],
            'ios.rv'         => ['present', 'string'],
            'ios.banner'     => ['present', 'string'],
            'ios.iv'         => ['present', 'string'],
            'ios.splash'     => ['present', 'string'],
        ];

        $this->validate($request, $rules);

        /* 这是创建逻辑，已有 Adapter 不允许被 Update 甚至是 Delete！ */
        /* 获取已有的记录和该厂商的信息 */
        $request->query->set('nw_firm_id', $request->get('nw_firm_id'));
        $request->query->set('publisher_id', $request->get('publisher_id'));
        $firmPublisherAdapters = $this->firmPublisherAdapters($request)->original;
        $firmPublisherAdapters = $firmPublisherAdapters['data'];

        $publisherId = $request->get('publisher_id');
        $nwFirmId = $request->get('nw_firm_id');

        /* Format 映射 */
        $formatMap = [
            'native' => FirmAdapter::FORMAT_NATIVE,
            'rv'     => FirmAdapter::FORMAT_RV,
            'banner' => FirmAdapter::FORMAT_BANNER,
            'iv'     => FirmAdapter::FORMAT_INTERSTITIAL,
            'splash' => FirmAdapter::FORMAT_SPLASH
        ];

        try {
            DB::beginTransaction();
            foreach (['android', 'ios'] as $platform) {
                $platformFormats = $request->get($platform);
                /* Format 传入参数名和顺序与 $firmPublisherAdapters 相同 */
                foreach ($formatMap as $formatParam => $format) {
                    /* 1 表示不支持该广告样式 */
                    $tmp = $firmPublisherAdapters[$platform][$formatParam];
                    if ($tmp['active'] == 1 || $tmp['adapter'] != '' || $platformFormats[$formatParam] == '') {
                        continue;
                    }
                    FirmAdapter::query()->create([
                        'publisher_id' => $publisherId,
                        'firm_id' => $nwFirmId,
                        'platform' => $platform == 'android' ? FirmAdapter::PLATFORM_ANDROID : FirmAdapter::PLATFORM_IOS,
                        'format' => $format,
                        'adapter' => $platformFormats[$formatParam],
                        'create_time' => time(),
                        'update_time' => time(),
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            return $this->transactionExceptionResponse($e);
        }

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
        $this->checkAccessPermission('firm-adapter@update');

        $this->validate($request, ['adapter' => 'present|string']);

        /* 只允许修改 Adapter 包名 */
        FirmAdapter::query()->where('id', $id)->update([
            'adapter' => $request->get('adapter'),
            'update_time' => time()
        ]);

        return $this->jsonResponse();
    }

    public function firmPublisherAdapters(Request $request): JsonResponse
    {
        $publisherIdList = Publisher::getAllPublisherId();
        $publisherIdList[] = 0; // 代表所有开发者
        $rules = [
            'nw_firm_id'   => ['required', 'exists:network_firm,id'],
            'publisher_id' => ['required', Rule::in($publisherIdList)],
        ];
        $this->validate($request, $rules);

        $data = NetworkFirm::query()
            ->from(NetworkFirm::TABLE . ' as t1')
            ->leftJoin(FirmAdapter::TABLE . ' as t2', 't2.firm_id', '=', 't1.id')
            ->select(['t1.id', 't1.name', 't1.native', 't1.rewarded_video as rv', 't1.banner', 't1.interstitial as iv', 't1.splash'])
            ->selectRaw("MAX(IF(t2.`platform` = 1 AND t2.`format` = 0 AND t2.`adapter` != '', t2.`adapter`, '')) AS `and_native`")
            ->selectRaw("MAX(IF(t2.`platform` = 1 AND t2.`format` = 1 AND t2.`adapter` != '', t2.`adapter`, '')) AS `and_rv`")
            ->selectRaw("MAX(IF(t2.`platform` = 1 AND t2.`format` = 2 AND t2.`adapter` != '', t2.`adapter`, '')) AS `and_banner`")
            ->selectRaw("MAX(IF(t2.`platform` = 1 AND t2.`format` = 3 AND t2.`adapter` != '', t2.`adapter`, '')) AS `and_iv`")
            ->selectRaw("MAX(IF(t2.`platform` = 1 AND t2.`format` = 4 AND t2.`adapter` != '', t2.`adapter`, '')) AS `and_splash`")
            ->selectRaw("MAX(IF(t2.`platform` = 2 AND t2.`format` = 0 AND t2.`adapter` != '', t2.`adapter`, '')) AS `ios_native`")
            ->selectRaw("MAX(IF(t2.`platform` = 2 AND t2.`format` = 1 AND t2.`adapter` != '', t2.`adapter`, '')) AS `ios_rv`")
            ->selectRaw("MAX(IF(t2.`platform` = 2 AND t2.`format` = 2 AND t2.`adapter` != '', t2.`adapter`, '')) AS `ios_banner`")
            ->selectRaw("MAX(IF(t2.`platform` = 2 AND t2.`format` = 3 AND t2.`adapter` != '', t2.`adapter`, '')) AS `ios_iv`")
            ->selectRaw("MAX(IF(t2.`platform` = 2 AND t2.`format` = 4 AND t2.`adapter` != '', t2.`adapter`, '')) AS `ios_splash`")
            ->groupBy(['t1.id', 't1.name', 't1.native', 't1.rewarded_video', 't1.banner', 't1.interstitial', 't1.splash'])
            ->where('t1.id', $request->query('nw_firm_id'))
            ->where('t2.publisher_id', $request->query('publisher_id'))
            ->first();

        if ($data === null) {
            /* 没有对该 Publisher 指定过 Adapter 的话，构造个空的 */
            $data = NetworkFirm::query()
                ->select(['id', 'name', 'native', 'rewarded_video as rv', 'banner', 'interstitial as iv', 'splash'])
                ->where('id', $request->query('nw_firm_id'))->firstOrFail();
            $fakeAdapters = [
                'native' => ['adapter' => '', 'active' => $data['native'] + 1,],
                'rv'     => ['adapter' => '', 'active' => $data['rv']     + 1,],
                'banner' => ['adapter' => '', 'active' => $data['banner'] + 1,],
                'iv'     => ['adapter' => '', 'active' => $data['iv']     + 1,],
                'splash' => ['adapter' => '', 'active' => $data['splash'] + 1,],
            ];
            return $this->jsonResponse([
                'label'   => $data['name'],
                'value'   => $data['id'],
                'android' => $fakeAdapters,
                'ios'     => $fakeAdapters
            ]);
        }

        $res = [
            'label' => $data['name'],
            'value' => $data['id'],
            'android' => [
                'native' => ['adapter' => $data['and_native'], 'active' => $data['native'] + 1,],
                'rv'     => ['adapter' => $data['and_rv'],     'active' => $data['rv'] + 1,],
                'banner' => ['adapter' => $data['and_banner'], 'active' => $data['banner'] + 1,],
                'iv'     => ['adapter' => $data['and_iv'],     'active' => $data['iv'] + 1,],
                'splash' => ['adapter' => $data['and_splash'], 'active' => $data['splash'] + 1,],
            ],
            'ios' => [
                'native' => ['adapter' => $data['ios_native'], 'active' => $data['native'] + 1,],
                'rv'     => ['adapter' => $data['ios_rv'],     'active' => $data['rv'] + 1,],
                'banner' => ['adapter' => $data['ios_banner'], 'active' => $data['banner'] + 1,],
                'iv'     => ['adapter' => $data['ios_iv'],     'active' => $data['iv'] + 1,],
                'splash' => ['adapter' => $data['ios_splash'], 'active' => $data['splash'] + 1,],
            ]
        ];

        return $this->jsonResponse($res);
    }
}
