<?php

namespace App\Http\Controllers;

use App\Models\MySql\PublisherGroup;
use App\Rules\NotExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 考虑到 PublisherGroup 相关设置以后可能会拓展，因此不写在 Publisher 中
 *
 * Class PublisherGroupController
 * @package App\Http\Controllers
 * @author  zohar
 * @date    2020/6/10
 */
class PublisherGroupController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAccessPermission('publisher-group@store');

        $this->validate($request, ['name' => ['required', 'string', new NotExists('publisher_group', 'name')]]);

        $id = PublisherGroup::query()->insertGetId([
            'name' => $request->get('name'),
            'type' => PublisherGroup::TYPE_SDK_DISTRIBUTION,
        ]);

        return $this->jsonResponse(['id' => $id], 1);
    }
}