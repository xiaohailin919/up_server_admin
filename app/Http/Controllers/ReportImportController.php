<?php

namespace App\Http\Controllers;

use App\Models\MySql\Network;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\ReportImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportImportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = ReportImport::query()
            ->from(ReportImport::TABLE .' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t3', 't3.id', '=', 't1.firm_id')
            ->leftJoin(Network::TABLE . ' as t4', 't4.id', '=', 't1.network_id')
            ->select([
                't1.id', 't1.publisher_id', 't2.name as publisher_name', 't1.firm_id as nw_firm_id', 't3.name as nw_firm_name',
                't1.network_id', 't4.name as network_name', 't1.create_time', 't1.import_time', 't1.status', 't1.import_all_time'
            ])
            ->orderByDesc('t1.create_time')
        ;
        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if ($request->has('publisher_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('publisher_name') . '%');
        }
        if ($request->query('nw_firm_id', -1) > 0) {
            $query->where('t1.firm_id', $request->query('nw_firm_id'));
        }
        if (array_key_exists($request->query('status', -1), ReportImport::getStatusMap())) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            /* 开始日期，结束日期 */
            $importAllTime = json_decode($datum['import_all_time'], true);
            $importAllTime = array_values(array_sort($importAllTime));
            if (count($importAllTime) == 0) {
                $data['list'][$idx]['end_date'] = $data['list'][$idx]['start_date'] = '-';
            } else if (count($importAllTime) == 1) {
                $data['list'][$idx]['end_date'] = $data['list'][$idx]['start_date'] = $importAllTime[0];
            } else {
                $data['list'][$idx]['start_date'] = $importAllTime[0];
                $data['list'][$idx]['end_date']   = $importAllTime[count($importAllTime) - 1];
            }

            unset($data['list'][$idx]['import_all_time']);
        }

        return $this->jsonResponse($data);
    }

    /**
     * 获取已上传过报表的厂商元数据
     */
    public function metaNwFirm(): JsonResponse
    {
        $data = ReportImport::query()->from(ReportImport::TABLE . ' as t1')
            ->leftJoin(NetworkFirm::TABLE . ' as t2', 't2.id', '=', 't1.firm_id')
            ->select(['t1.firm_id as value', 't2.name as label'])
            ->groupBy(['t1.firm_id', 't2.name'])
            ->get()
            ->toArray();
        foreach ($data as $idx => $datum) {
            if ($datum['value'] >= NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY) {
                $data[$idx]['label'] = $datum['value'] . ' | ' . $datum['label'];
            }
        }
        return $this->jsonResponse($data);
    }
}
