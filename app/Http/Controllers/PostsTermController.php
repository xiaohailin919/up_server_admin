<?php

namespace App\Http\Controllers;


use App\Models\MySql\PostsTerm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostsTermController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('posts-term@index');

        $query = PostsTerm::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->has('slug')) {
            $query->where('slug', 'like', '%' . $request->query('slug') . '%');
        }
        if (array_key_exists($request->query('type', -1), PostsTerm::getTypeMap())) {
            $query->where('type', $request->query('type'));
        }
        if (in_array($request->query('popular', -1), [PostsTerm::POPULAR_NOT, PostsTerm::POPULAR_YES], false)) {
            $query->where('popular', $request->query('popular'));
        }
        if (in_array($request->query('order_by', 'update_time'), ['update_time', 'rank'], true)) {
            $query->orderByDesc($request->query('order_by', 'update_time'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no'));

        $data = $this->parseResByPaginator($paginator);

        return $this->jsonResponse($data);
    }

    /**
     * 获取元数据
     *
     * @return JsonResponse
     */
    public function meta(): JsonResponse
    {
        $this->checkAccessPermission('posts-term@index');

        $postsTerms = PostsTerm::query()->get()->toArray();

        $res = ['category_list' => [], 'tag_list' => []];
        foreach ($postsTerms as $postsTerm) {
            $tmp = ['id' => $postsTerm['id'], 'name' => $postsTerm['name'], 'slug' => $postsTerm['slug']];
            if ($postsTerm['type'] == PostsTerm::TYPE_CATEGORY) {
                $res['category_list'][] = $tmp;
            } else {
                $res['tag_list'][] = $tmp;
            }
        }

        return $this->jsonResponse($res);
    }

    /**
     * 单条记录
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $record = PostsTerm::query()->where('id', $id)->firstOrFail();
        return $this->jsonResponse($record);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'type'        => ['required', Rule::in(array_keys(PostsTerm::getTypeMap()))],
            'name'        => ['required', 'string'],
            'slug'        => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'popular'     => ['nullable', Rule::in([PostsTerm::POPULAR_YES, PostsTerm::POPULAR_NOT])],
            'rank'        => ['nullable', 'integer'],
        ];
        $this->validate($request, $rules);
        $this->validateUnique($request);

        $model = PostsTerm::query()->create([
            'type' => $request->get('type'),
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
            'description' => $request->get('description', ''),
            'popular' => $request->get('popular', PostsTerm::POPULAR_NOT),
            'rank' => $request->get('rank', 0),
        ]);

        return $this->jsonResponse($model, 1);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $postsTerm = PostsTerm::query()->where('id', $id)->firstOrFail();

        $rules = [
            'type'        => ['required', 'integer', 'size:' . $postsTerm['type']],
            'name'        => ['required', 'string'],
            'slug'        => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'popular'     => ['nullable', Rule::in([PostsTerm::POPULAR_YES, PostsTerm::POPULAR_NOT])],
            'rank'        => ['nullable', 'integer'],
        ];
        $this->validate($request, $rules);
        $this->validateUnique($request, $postsTerm['id']);

        $postsTerm->update([
            'name'        => $request->get('name'),
            'slug'        => $request->get('slug'),
            'description' => $request->get('description', ''),
            'popular'     => $request->get('popular', PostsTerm::POPULAR_NOT),
            'rank'        => $request->get('rank', 0)
        ]);

        return $this->jsonResponse($postsTerm);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        PostsTerm::query()->where('id', $id)->delete();
        return $this->jsonResponse();
    }

    /**
     * 相同 Type 下，name 和 slug 必须唯一
     *
     * @param Request $request
     * @param int $id
     * @throws ValidationException
     */
    public function validateUnique(Request $request, $id = 0)
    {
        if (PostsTerm::query()->where('type', $request->get('type'))->where('name', $request->get('name'))->where('id', '!=', $id)->exists()) {
            throw new ValidationException(null, $this->jsonResponse(['name' => "Same name exists under type " . $request->get('type')], 10000));
        }
        if (PostsTerm::query()->where('type', $request->get('type'))->where('slug', $request->get('slug'))->where('id', '!=', $id)->exists()) {
            throw new ValidationException(null, $this->jsonResponse(['slug' => "Same slug exists under type " . $request->get('type')], 10000));
        }
    }
}
