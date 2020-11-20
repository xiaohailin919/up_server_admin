<?php

namespace App\Http\Controllers;

use App\Helpers\ReportInputFilter;
use App\Models\MySql\App;
use App\Models\MySql\Base;
use App\Models\MySql\PublisherGroup;
use App\Models\MySql\Users;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use App\Models\MySql\StrategyAppLogger;


class UploadRulesController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('upload_rules');

        $query = StrategyAppLogger::query()
            ->from('strategy_app_logger as t1')
            ->leftJoin('app as t2', 't2.id', '=', 't1.app_id')
            ->select([
                't1.id', 't1.rule_type', 't1.publisher_group_id', 't2.uuid as app_id',
                't1.manager as admin_id', 't1.update_time', 't1.status'
            ])
            ->selectRaw("IFNULL(t2.uuid, '') as app_id")
            ->selectRaw("IFNULL(t2.name, '') as app_name")
            ->orderByDesc('id');

        if ($request->has('app_uuid')) {
            $query->where('t2.uuid', $request->query('app_uuid'));
        }
        if ($request->has('app_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('app_name') . '%');
        }
        if (array_key_exists($request->query('publisher_group_id', -1), PublisherGroup::getPublisherGroupIdNameMap())) {
            $query->where('t1.publisher_group_id', $request->query('publisher_group_id'));
        }
        if (array_key_exists($request->query('status', -1), StrategyAppLogger::getStatusMap())) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $i => $datum) {
            $data['list'][$i]['publisher_group_name'] = PublisherGroup::getName($datum['publisher_group_id']);
            $data['list'][$i]['publisher_group_id'] = $datum['publisher_group_id'] == 0 ? '' : $datum['publisher_group_id'];
            $data['list'][$i]['admin_name'] = Users::getName($datum['admin_id']);
            unset($data['list'][$i]['admin_id']);
        }

        return $this->jsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('upload_rules_store');

        $rules = [
            'type'                      => ['required', Rule::in(array_keys(StrategyAppLogger::getTypeMap()))],           // 规则类型
            'app_uuid_list'             => ['required_if:type,' . StrategyAppLogger::RULE_TYPE_APP, 'array'],             // APP UUID
            'app_uuid_list.*'           => ['exists:app,uuid'],
            'publisher_group_id_list'   => ['required_if:type,' . StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP, 'array'], // 开发者群组
            'publisher_group_id_list.*' => ['exists:publisher_group,id'],
            'tk_max_amount'             => ['required', 'integer', 'min:1'],                                              // TK 上报条数
            'tk_interval'               => ['required', 'integer', 'min:0'],                                              // TK 上报间隔
            'da_max_amount'             => ['required', 'integer', 'min:1'],                                              // DA 上报条数
            'da_interval'               => ['required', 'integer', 'min:0'],                                              // DA 上报间隔
            'upload_interval'           => ['required', 'integer', 'min:0'],                                              // TC 上报间隔
            'tk_timer_switch'           => ['required', 'in:1,2'],                                                        // TK 定时器触发上报
            'da_rt_keys_ft'             => ['nullable', 'array'],                                                         // DA 上报规则
            'da_no_report_keys_by_rate' => ['nullable', 'array'],                                                         // DA 不上报规则
            'tk_no_report_keys_by_rate' => ['nullable', 'array'],                                                         // TK 不上报规则
            'tk_address'                => ['nullable', 'string'],                                                        // TK 服务器地址
            'da_address'                => ['nullable', 'string'],                                                        // DA 服务器地址
            'tcp_domain'                => ['required', 'string'],                                                        // TCP 服务器域名
            'tcp_port'                  => ['required', 'integer', 'min:0', 'max:65535'],                                 // TCP 服务器端口
            'tcp_tk_da_type'            => ['required', 'in:1,2'],                                                        // TCP 上报协议
            'tcp_tk_da_rate'            => ['required_if:tcp_tk_da_type,1', 'min:0', 'max:100'],                          // TCP 上报切量比例
            'status'                    => ['required', Rule::in(array_keys(StrategyAppLogger::getStatusMap()))],         // 状态
        ];
        $this->validate($request, $rules);
        $this->validateUploadRules($request);

        $type = $request->input('type');

        $appIds = $type == StrategyAppLogger::RULE_TYPE_APP ? ReportInputFilter::getAppIdsByUuids($request->input('app_uuid_list')) : [0];
        $publisherGroupIds = $type == StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP ? $request->input('publisher_group_id_list') : [0];

        try {
            DB::beginTransaction();

            foreach ($appIds as $appId) {
                foreach ($publisherGroupIds as $publisherGroupId) {
                    StrategyAppLogger::query()->updateOrCreate([
                        'rule_type' => $type,
                        'app_id' => $appId,
                        'publisher_group_id' => $publisherGroupId
                    ], [
                        'tk_address'                => $request->input('tk_address', ''),
                        'tk_max_amount'             => $request->input('tk_max_amount'),
                        'tk_interval'               => $request->input('tk_interval'),
                        'tk_timer_switch'           => $request->input('tk_timer_switch'),
                        'da_address'                => $request->input('da_address', ''),
                        'da_max_amount'             => $request->input('da_max_amount'),
                        'da_interval'               => $request->input('da_interval'),
                        'upload_interval'           => $request->input('upload_interval'),
                        'tcp_domain'                => $request->input('tcp_domain'),
                        'tcp_port'                  => $request->input('tcp_port'),
                        'tcp_tk_da_type'            => $request->input('tcp_tk_da_type'),
                        'tcp_tk_da_rate'            => $request->input('tcp_tk_da_rate'),
                        'status'                    => $request->input('status'),
                        'da_rt_keys_ft'             => json_encode($request->input('da_rt_keys_ft', [])),
                        'da_no_report_keys_by_rate' => json_encode($request->input('da_no_report_keys_by_rate', [])),
                        'tk_no_report_keys_by_rate' => json_encode($request->input('tk_no_report_keys_by_rate', [])),
                        'da_rt_keys'                => json_encode([]),
                        'da_not_keys'               => json_encode([]),
                        'tracking_not_types'        => json_encode([]),
                        'manager'                   => auth('api')->id(),
                        'upload_address'            => ''
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

    /**
     * 单个信息
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $data = StrategyAppLogger::query()->find($id);

        if ($data == null) {
            return $this->jsonResponse([], 10003);
        }

        $data['type']                 = $data['rule_type'];
        $data['app_uuid']             = App::query()->where('id', $data['app_id'])->value('uuid') ?? 0;
        $data['app_name']             = App::getName($data['app_id']);
        $data['publisher_group_name'] = PublisherGroup::getName($data['publisher_group_id']);
        $data['da_rt_keys_ft']             = json_decode($data['da_rt_keys_ft'], true);
        $data['da_no_report_keys_by_rate'] = json_decode($data['da_no_report_keys_by_rate'], true);
        $data['tk_no_report_keys_by_rate'] = json_decode($data['tk_no_report_keys_by_rate'], true);

        /* 兼容旧数据，将 format 和 rate 转为数字显示 */
        $daRtKeysFt = $daNoReportKeysByRate = $tkNoReportKeysByRate = [];
        foreach ($data['da_rt_keys_ft'] as $key => $formats) {
            foreach ($formats as $format) {
                $daRtKeysFt[$key][] = (int)$format;
            }
        }
        foreach ($data['da_no_report_keys_by_rate'] as $key => $datum) {
            foreach ($datum['formats'] as $format) {
                $daNoReportKeysByRate[$key]['formats'][] = (int)$format;
            }
            $daNoReportKeysByRate[$key]['rate'] = (int)$datum['rate'];
        }
        foreach ($data['tk_no_report_keys_by_rate'] as $key => $datum) {
            foreach ($datum['formats'] as $format) {
                $tkNoReportKeysByRate[$key]['formats'][] = (int)$format;
            }
            $tkNoReportKeysByRate[$key]['rate'] = (int)$datum['rate'];
        }
        $data['da_rt_keys_ft'] = $daRtKeysFt;
        $data['da_no_report_keys_by_rate'] = $daNoReportKeysByRate;
        $data['tk_no_report_keys_by_rate'] = $tkNoReportKeysByRate;

        unset(
            $data['rule_type'], $data['app_id'], $data['da_not_keys'], $data['da_rt_keys'], $data['tracking_not_types'],
            $data['da_not_keys_ft'], $data['tk_no_t_ft'], $data['upload_address']
        );

        return $this->jsonResponse($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('upload_rules_update');

        $data = StrategyAppLogger::query()->find($id);

        if ($data == null) {
            return $this->jsonResponse([], 10003);
        }

        $rules = [
            'tk_max_amount'             => ['required', 'integer', 'min:1'],                                              // TK 上报条数
            'tk_interval'               => ['required', 'integer', 'min:0'],                                              // TK 上报间隔
            'da_max_amount'             => ['required', 'integer', 'min:1'],                                              // DA 上报条数
            'da_interval'               => ['required', 'integer', 'min:0'],                                              // DA 上报间隔
            'upload_interval'           => ['required', 'integer', 'min:0'],                                              // TC 上报间隔
            'tk_timer_switch'           => ['required', 'in:1,2'],                                                        // TK 定时器触发上报
            'da_rt_keys_ft'             => ['nullable', 'array'],                                                          // DA 上报规则
            'da_no_report_keys_by_rate' => ['nullable', 'array'],                                                          // DA 不上报规则
            'tk_no_report_keys_by_rate' => ['nullable', 'array'],                                                          // TK 不上报规则
            'tk_address'                => ['nullable', 'string'],                                                        // TK 服务器地址
            'da_address'                => ['nullable', 'string'],                                                        // DA 服务器地址
            'tcp_domain'                => ['required', 'string'],                                                        // TCP 服务器域名
            'tcp_port'                  => ['required', 'integer', 'min:0', 'max:65535'],                                 // TCP 服务器端口
            'tcp_tk_da_type'            => ['required', 'in:1,2'],                                                        // TCP 上报协议
            'tcp_tk_da_rate'            => ['required_if:tcp_tk_da_type,1', 'min:0', 'max:100'],                                              // TCP 上报切量比例
            'status'                    => ['required', Rule::in(array_keys(StrategyAppLogger::getStatusMap()))],         // 状态
        ];
        $this->validate($request, $rules);
        $this->validateUploadRules($request);

        $data->update([
            'tk_address'                => $request->input('tk_address', ''),
            'tk_max_amount'             => $request->input('tk_max_amount'),
            'tk_interval'               => $request->input('tk_interval'),
            'tk_timer_switch'           => $request->input('tk_timer_switch'),
            'da_address'                => $request->input('da_address', ''),
            'da_max_amount'             => $request->input('da_max_amount'),
            'da_interval'               => $request->input('da_interval'),
            'upload_interval'           => $request->input('upload_interval'),
            'tcp_domain'                => $request->input('tcp_domain'),
            'tcp_port'                  => $request->input('tcp_port', 80),
            'tcp_tk_da_type'            => $request->input('tcp_tk_da_type'),
            'tcp_tk_da_rate'            => $request->input('tcp_tk_da_rate'),
            'status'                    => $request->input('status'),
            'da_rt_keys_ft'             => json_encode($request->input('da_rt_keys_ft', [])),
            'da_no_report_keys_by_rate' => json_encode($request->input('da_no_report_keys_by_rate', [])),
            'tk_no_report_keys_by_rate' => json_encode($request->input('tk_no_report_keys_by_rate', [])),
            'da_rt_keys'                => json_encode([]),
            'da_not_keys'               => json_encode([]),
            'tracking_not_types'        => json_encode([]),
            'manager'                   => auth('api')->id(),
            'upload_address'            => ''
        ]);

        return $this->jsonResponse();
    }

    /**
     * 校验三个上报规则，校验失败抛出异常
     *
     * @param Request $request
     * @throws ValidationException
     */
    private function validateUploadRules(Request $request)
    {
        /* 校验三个上报规则 */
        if (!empty($request->input('da_rt_keys_ft', ''))) {
            $daUploadRules = $request->input('da_rt_keys_ft');
            foreach ($daUploadRules as $daKey => $formats) {
                foreach ($formats as $key => $format) {
                    if (!array_key_exists($format, Base::getFormatMap())) {
                        throw new ValidationException(null, $this->jsonResponse(['da_rt_keys_ft.' . $daKey . '.' . $key => 'format error.'], 10000));
                    }
                }
            }
        }
        if (!empty($request->input('da_no_report_keys_by_rate', ''))) {
            $daNoUploadRules = $request->input('da_no_report_keys_by_rate');
            foreach ($daNoUploadRules as $daKey => $daNoUploadRule) {
                foreach ($daNoUploadRule['formats'] as $key => $format) {
                    if (!array_key_exists($format, Base::getFormatMap())) {
                        throw new ValidationException(null, $this->jsonResponse(['da_no_report_keys_by_rate.' . $daKey . '.format.' . $key => 'format error.'], 10000));
                    }
                }
                if (empty($daNoUploadRule['rate']) || !is_numeric($daNoUploadRule['rate'])) {
                    throw new ValidationException(null, $this->jsonResponse(['da_no_report_keys_by_rate.' . $daKey . '.rate' => 'Rate error'], 10000));
                }
            }
        }
        if (!empty($request->input('tk_no_report_keys_by_rate', ''))) {
            $tkNoUploadRules = $request->input('tk_no_report_keys_by_rate');
            foreach ($tkNoUploadRules as $tkKey => $tkNoUploadRule) {
                foreach ($tkNoUploadRule['formats'] as $key => $format) {
                    if (!array_key_exists($format, Base::getFormatMap())) {
                        throw new ValidationException(null, $this->jsonResponse(['tk_no_report_keys_by_rate.' . $tkKey . '.format.' . $key => 'format error.'], 10000));
                    }
                }
                if (empty($tkNoUploadRule['rate']) || !is_numeric($tkNoUploadRule['rate'])) {
                    throw new ValidationException(null, $this->jsonResponse(['tk_no_report_keys_by_rate.' . $tkKey . '.rate' => 'Rate error'], 10000));
                }
            }
        }
    }
}
