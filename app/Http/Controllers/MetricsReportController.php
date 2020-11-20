<?php

namespace App\Http\Controllers;

use App\Models\MySql\MetricsReport;
use App\Rules\Lowercase;
use App\Rules\NotExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetricsReportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('metrics_report@index');

        $query = MetricsReport::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->has('field')) {
            $query->where('field', 'like', '%' . $request->query('field') . '%');
        }
        if (array_key_exists($request->query('kind', -1), MetricsReport::getKindMap())) {
            $query->where('kind', $request->query('kind'));
        }
        if (in_array($request->query('is_default', -1), [MetricsReport::IS_DEFAULT, MetricsReport::IS_DEFAULT_NOT], false)) {
            $query->where('is_default', $request->query('is_default'));
        }
        if ($request->has('order_by_field_list') && $request->has('order_by_direction_list')) {
            $orderByFields = $request->query('order_by_field_list');
            $orderByDirections = $request->query('order_by_direction_list');
            foreach ($orderByFields as $idx => $orderByField) {
                if (in_array($orderByField, ['id', 'name', 'field', 'group', 'is_default', 'show_priority', 'priority'], true)) {
                    $query->orderBy($orderByField, $orderByDirections[$idx]);
                }
            }
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

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
        $this->checkAccessPermission('metrics_report@index');

        $metricsReport = MetricsReport::query()->where('id', $id)->firstOrFail();
        return $this->jsonResponse($metricsReport);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('metrics_report@store');

        $rules =  [
            'name'          => ['required', 'string', new NotExists(MetricsReport::TABLE, 'name', [], 'kind = ' . $request->input('kind', 1))],
            'field'         => ['required', 'string', 'different:name', new Lowercase(), new NotExists(MetricsReport::TABLE, 'field', [], 'kind = ' . $request->input('kind', 1))],
            'kind'          => ['required', Rule::in(array_keys(MetricsReport::getKindMap()))],
            'is_default'    => ['required', 'boolean'],
            'group'         => ['integer'],
            'show_priority' => ['required', 'integer'],
            'priority'      => ['required', 'integer']
        ];
        $this->validate($request, $rules);

        $metricsReport = MetricsReport::query()->create([
            'name'          => $request->input('name'),
            'field'         => $request->input('field'),
            'kind'          => $request->input('kind'),
            'is_default'    => $request->input('is_default'),
            'group'         => $request->input('group', 0),
            'show_priority' => $request->input('show_priority'),
            'priority'      => $request->input('priority')
        ]);

        return $this->jsonResponse($metricsReport, 1);
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
        $this->checkAccessPermission('metrics_report@update');

        $metricsReport = MetricsReport::query()->where('id', $id)->firstOrFail();

        $rules = [
            'name'       => ['nullable', 'string', new NotExists(MetricsReport::TABLE, 'name', [$id], 'kind = ' . $metricsReport['kind'])],
            'field'      => ['nullable', 'string', new Lowercase(), new NotExists(MetricsReport::TABLE, 'field', [$id], 'kind = ' . $metricsReport['kind'])],
            'is_default' => ['nullable', 'boolean'],
            'group'      => ['nullable', 'integer'],
            'priority'   => ['nullable', 'integer'],
            'show_priority' => ['nullable', 'integer'],
        ];

        $this->validate($request, $rules);

        $metricsReport->update([
            'name'          => $request->has('name') ? $request->input('name') : $metricsReport['name'],
            'field'         => $request->has('field') ? $request->input('field') : $metricsReport['field'],
            'is_default'    => $request->has('is_default') ? $request->input('is_default') : $metricsReport['is_default'],
            'group'         => $request->has('group') ? $request->input('group') : $metricsReport['group'],
            'priority'      => $request->has('priority') ? $request->input('priority') : $metricsReport['priority'],
            'show_priority' => $request->has('show_priority') ? $request->input('show_priority') : $metricsReport['show_priority']
        ]);

        return $this->jsonResponse($metricsReport);
    }

    /**
     * 删除
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->checkAccessPermission('metrics_report@destroy');
        MetricsReport::query()->where('id', $id)->delete();
        return $this->jsonResponse();
    }
}
