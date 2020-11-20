<?php

namespace App\Http\Controllers;

use App\Models\MySql\Subscriber;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscribersController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Subscriber::query()->orderByDesc('create_time');

        if (in_array($request->query('language', -1), Subscriber::getLanguageList(), false)) {
            $query->where('language', '=', $request->query('language'));
        }
        if (in_array($request->query('status', -1), Subscriber::getStatusList(), false)) {
            $query->where('unsubscribe', '=', $request->query('status'));
        }
        if (in_array($request->query('type', -1), Subscriber::getSubscriberTypeList(), false)) {
            $query->where('subscribe_type', '=', $request->query('type'));
        }
        if ($request->has('email')) {
            $query->where('email', '=', $request->query('email'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        return $this->jsonResponse($this->parseResByPaginator($paginator));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'type' => ['required', 'integer', Rule::in(Subscriber::getSubscriberTypeList())],
            'language' => ['required', 'string', Rule::in(Subscriber::getLanguageList())],
            'email_list' => ['required', 'array'],
            'email_list.*' => 'email',
        ];

        $this->validate($request, $rules);

        /* 获取已存在的邮箱 */
        $existed = Subscriber::query()->whereIn('email', $request->input('email_list'))->get()->toArray();
        $existed = array_column($existed, 'email');

        try {
            DB::beginTransaction();
            foreach ($request['email_list'] as $email) {
                if (in_array($email, $existed, true)) {
                    continue;
                }
                Subscriber::query()->create([
                    'subscribe_type' => $request['type'], 'language' => $request['language'], 'email' => $email,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return empty($existed)
            ? $this->jsonResponse([], 1)
            : $this->jsonResponse($existed, 1, '以下邮箱已存在，不再重复保存');
    }
}
