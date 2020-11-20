<?php


namespace App\Http\Controllers;


use App\Helpers\Export;
use App\Models\MySql\Contact;
use App\Models\MySql\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ContactController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('contact@index');

        $query = Contact::query()
            ->select([
                'id', 'name', 'email', 'language', 'area_code', 'phone', 'company',
                'job_info', 'message', 'remark', 'status', 'update_time', 'admin_id'
            ])->orderByDesc('id');

        if ($request->query('admin_id', 0) > 0) {
            $query->where('admin_id', $request->query('admin_id'));
        }
        if (array_key_exists($request->query('language', -1), Contact::getLanguageMap())) {
            $query->where('language', $request->query('language'));
        }
        if (array_key_exists($request->query('status', -1), Contact::getStatusMap())) {
            $query->where('status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 50), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            $adminName = Users::getName($datum['admin_id']);
            $data['list'][$idx]['admin_name'] = $adminName == 'unknown' ? '' : $adminName;
        }

        return $this->jsonResponse($data);
    }

    /**
     * 数据导出 API，直接写入为 excel 文件
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $request->query->set('page_no', 1);
        $request->query->set('page_size', 5000);
        $response = json_decode($this->index($request)->content(), true);
        $data = $response['data']['list'];
        $headerMap = [
            'id'         => __('common.id'),
            'name'       => __('common.contact.name'),
            'email'      => __('common.contact.email'),
            'language'   => __('common.contact.language'),
            'phone'      => __('common.contact.phone'),
            'company'    => __('common.contact.company'),
            'job_info'   => __('common.contact.job_info'),
            'message'    => __('common.contact.message'),
            'remark'     => __('common.contact.remark'),
            'status'     => __('common.contact.status'),
            'admin_name' => __('common.contact.admin_name')
        ];

        foreach ($data as $key => $datum) {
            $data[$key]['phone']    = $datum['area_code'] . ' ' . $datum['phone'];
            $data[$key]['language'] = Contact::getLanguageName($datum['language']);
            $data[$key]['status']   = Contact::getStatusName($datum['status']);
            unset($data[$key]['area_code'], $data[$key]['update_time'], $data[$key]['admin_id']);
        }

        Export::exportAsCsv($data, $headerMap, 'contact_' . date('Y_m_d_H_i_s') . '.csv');
    }

    /**
     * 获取已联系的联系人的元数据
     *
     * @return JsonResponse
     */
    public function metaContactor(): JsonResponse
    {
        $data = Contact::query()
            ->leftJoin('users', 'users.id', '=', 'contact.admin_id')
            ->where('contact.admin_id', '!=', 0)
            ->where('contact.status', Contact::STATUS_PROCESSED)
            ->groupBy(['contact.admin_id', 'users.name'])
            ->get(['contact.admin_id as id', 'users.name as name'])
            ->toArray();
        $res = [];
        foreach ($data as $datum) {
            $res[] = ['label' => $datum['name'], 'value' => $datum['id']];
        }
        return $this->jsonResponse($res);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('contact@index');

        $contact = Contact::query()->where('id', $id)->firstOrFail();

        $rules = [
            'status' => ['nullable', Rule::in(array_keys(Contact::getStatusMap()))],
            'remark' => ['nullable', 'string']
        ];

        $this->validate($request, $rules);

        if ($request->has('remark')) {
            $contact->update([
                'remark' => $request->input('remark'),
                'admin_id' => auth()->id(),
            ]);
        } else if ($request->has('status')) {
            $contact->update([
                'status' => $request->input('status'),
                'admin_id' => auth()->id(),
            ]);
        }

        return $this->jsonResponse();
    }
}