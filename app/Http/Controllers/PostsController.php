<?php

namespace App\Http\Controllers;

use App\Helpers\Export;
use App\Mail\EdmEmail;
use App\Models\MySql\Contact;
use App\Models\MySql\Posts;
use App\Models\MySql\PostsTerm;
use App\Models\MySql\Publisher;
use App\Models\MySql\Subscriber;
use App\Services\PostsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostsController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) {

        $this->checkAccessPermission('posts@index');

        $selectList = [
            "id", "type", "language", "title", "keyword", "description", "location", "event_time",
            "thumbnail", "redirect_url", "source", "popular", "views", "rank", "send_email",
            "send_email_subscriber", "send_email_success", "post_time", "create_time", "update_time",
            "status", "send_email_open", "send_email_redirect", "send_email_unsubscribe", 'admin_id'
        ];

        $isExport = $request->query('is_export', Posts::EXPORT_NOT) == Posts::EXPORT_YES;

        $query = Posts::query()->orderByDesc($request->query('order_by', 'update_time'));

        if (array_key_exists($request->query('type', Posts::TYPE_NEWS), Posts::getTypeMap())) {
            $query->where('type', '=', $request->query('type', Posts::TYPE_NEWS));
        }
        if (array_key_exists($request->query('language', Posts::LANGUAGE_CHINESE), Posts::getLanguageMap())) {
            $query->where('language', '=', $request->query('language', Posts::LANGUAGE_CHINESE));
        }
        if (array_key_exists($request->query('status', -1), Posts::getStatusMap())) {
            $query->where('status', '=', $request->query('status'));
        }
        if (array_key_exists($request->query('email_status', -1), Posts::getEmailStatusMap())) {
            $query->where('send_email', '=', $request->query('email_status'));
        }
        if (in_array($request->query('popular', -1), Posts::getPopularList(), false)) {
            $query->where('popular', '=', $request['popular']);
        }
        if ($request->has('post_time')) {
            $query->whereRaw("DATE_FORMAT(post_time, '%Y-%m-%d %H:%i:%s') >= '" . date('Y-m-d H:i:s', $request['post_time'] / 1000) . "'")
                ->whereRaw("DATE_FORMAT(post_time, '%Y-%m-%d %H:%i:%s') <= '" . date('Y-m-d H:i:s', $request['post_time'] / 1000 + 3600 * 24) . "'");
        }
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request['title'] . '%');
        }

        $paginator = $query->paginate($isExport ? 5000 : $request->get('page_size', 10), $selectList, 'page_no', $request->get('page_no', 1));
        $res = $this->parseResByPaginator($paginator);

        /* 导出报告 */
        if ($isExport) {
            $exportHeaderMap = PostsService::generateExportHeader();
            $exportData = PostsService::generateExportData($res['list']);
            Export::exportAsCsv($exportData, $exportHeaderMap, 'posts_' . date('Y-m-d H:i:s') . '.csv');
            return null;
        }

        /* 正常列表 */
        $res['list'] = PostsService::filterListData($res['list']);
        return $this->jsonResponse($res);
    }

    /**
     * 返回单条记录
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse {

        $this->checkAccessPermission('posts@index');

        $post = Posts::query()->find($id);

        if ($post == null) {
            return $this->jsonResponse([], 16001);
        }

        $res = PostsService::filterListData([$post]);
        return $this->jsonResponse($res[0]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        /* 验证规则，只验证类型，不去验证是否必填，如果没填直接塞默认值，应当让填写者掌控 */
        $rules = [
            'title'         => ['required', 'string'],
            'type'          => ['required', 'integer', Rule::in(array_keys(Posts::getTypeMap()))],
            'language'      => ['required', 'string', Rule::in(array_keys(Posts::getLanguageMap()))],
            'content'       => ['required_unless:type,' . Posts::TYPE_EVENT, 'string'],
            'rank'          => ['integer', 'in:0,1,2,3,4,5,6,7,8,9'],
            'status'        => ['required', 'integer', Rule::in(array_keys(Posts::getStatusMap()))],
            'post_time'     => ['required_if:status,'. Posts::STATUS_PUBLISHED],
            'keyword_list'  => ['nullable', 'array'],
            'category_list' => ['nullable', 'array'],
            'tag_list'      => ['nullable', 'array'],
        ];

        Validator::make($request->all(), $rules)->validate();

        /* 生产环境去掉这一句 */
        DB::select("set @@sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

        $posts = [];
        $posts['title']       = $request->input('title', '');
        $posts['type']        = $request->input('type', Posts::TYPE_NEWS);
        $posts['language']    = $request->input('language', Posts::LANGUAGE_CHINESE);
        $posts['content']     = $request->input('content', '');
        $posts['source']      = $request->input('source', '');
        $posts['popular']     = $request->input('popular', Posts::POPULAR_NO);
        $posts['views']       = $request->input('views', 0);
        $posts['rank']        = $request->input('rank', 0);
        $posts['send_email']  = Posts::EMAIL_NOT;
        $posts['status']      = $request->input('status', Posts::STATUS_DRAFT);
        $posts['update_time'] = date('Y-m-d H:i:s');
        $posts['admin_id']    = auth('api')->id();
        /* 处理缩略图 */
        $posts['thumbnail'] = empty($request->input('thumbnail', '')) ? '' : substr($request['thumbnail'], strpos($request['thumbnail'], 'posts'));
        /* 如果直接发布，那 Post Time 为当前 */
        $posts['post_time'] = date('Y-m-d H:i:s', $request->get('post_time', time() * 1000) / 1000);

        switch ($posts['type']) {
            case Posts::TYPE_NEWS: PostsService::storeNews($posts, $request);break;
            case Posts::TYPE_EVENT: PostsService::storeEvent($posts, $request);break;
            case Posts::TYPE_REPORT: PostsService::storeReport($posts, $request);break;
            case Posts::TYPE_EMAIL: PostsService::storeEmail($posts, $request);break;
        }

        return $this->jsonResponse([], 1);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Request $request, $id): JsonResponse {
        $posts = Posts::query()->find($id);

        if ($posts == null || !$posts instanceof Posts) {
            return $this->jsonResponse(['id' => 'Post id not found'], 16001);
        }

        /* 验证规则，只验证类型，不去验证是否必填，如果没填直接塞默认值，应当让填写者掌控 */
        $rules = [
            'title'              => ['required', 'string'],
            'language'           => ['required', 'string', Rule::in(array_keys(Posts::getLanguageMap()))],
            'content'            => ['required_unless:type,' . Posts::TYPE_EVENT, 'string'],
            'rank'               => ['integer', 'in:0,1,2,3,4,5,6,7,8,9'],
            'status'             => ['required', 'integer', Rule::in(array_keys(Posts::getStatusMap()))],
            'post_time'          => ['required_if:status,' . Posts::STATUS_PUBLISHED],
            'news_keyword_list'  => ['nullable', 'array'],
            'news_category_list' => ['nullable', 'array'],
            'news_tag_list'      => ['nullable', 'array'],
        ];
        Validator::make($request->all(), $rules)->validate();

        /* 发布时间 */
        if ($request->input('status', Posts::STATUS_DRAFT) == Posts::STATUS_PUBLISHED) {
            $posts['post_time'] = date('Y-m-d H:i:s', $request->input('post_time', time() * 1000) / 1000);
        }

        $posts->fill($request->except([
            'news_category_list', 'news_tag_list', 'news_keyword_list', 'news_description', 'event_location',
            'event_time', 'report_information', 'report_download_url', 'email_btn_text', 'email_redirect_url',
            'admin_id', 'admin_name', 'token', 'post_time'
        ]));

        /* 处理缩略图 */
        $posts['thumbnail'] = empty($posts['thumbnail']) ? '' : substr($posts['thumbnail'], strpos($posts['thumbnail'], 'posts'));
        $posts['update_time'] = date('Y-m-d H:i:s');
        $posts['admin_id'] = Auth::id();

        switch ($posts['type']) {
            case Posts::TYPE_NEWS: PostsService::updateNews($posts, $request);break;
            case Posts::TYPE_EVENT: PostsService::updateEvent($posts, $request);break;
            case Posts::TYPE_REPORT: PostsService::updateReport($posts, $request);break;
            case Posts::TYPE_EMAIL: PostsService::updateEmail($posts, $request);break;
        }

        return $this->jsonResponse();
    }

    public function sendEmail(Request $request, $id): JsonResponse {

        $this->validate($request, [
            'email_list' => 'required_if:type,2|array',
            'email_list.*' => 'nullable|email'
        ]);

        $posts = Posts::query()->find($id);

        if ($posts === null || !$posts instanceof Posts) {
            return $this->jsonResponse(['id' => 'Could not found post by ID: ' . $id], 16001);
        }

        /* 发送类型为 1 的话为全部发送，默认发送指定列表 */
        if ($request->input('type', 2) == 1) {
            if (env('APP_ENV') == 'test') {
                return $this->jsonResponse([], 9006);
            }

            /* 获取开发者用户 */
            $publishers = Publisher::query()
                ->where('status', Publisher::STATUS_RUNNING) // 匹配开发者状态
                ->get(['email'])->toArray();
            /* 获取 Contact 用户 */
            $contacts = Contact::query()
                ->where('language', '=', $posts['language']) // 匹配 Contact 用户语言
                ->get(['email'])->toArray();
            /* 获取订阅用户 */
            $subscribers = Subscriber::query()
                ->where('unsubscribe', '=', Subscriber::STATUS_SUBSCRIBE) // 匹配已订阅用户
                ->where('language', '=', $posts['language']) // 匹配订阅用户语言
                ->whereIn('subscribe_type', [ // 匹配订阅类型
                    Subscriber::SUBSCRIBE_TYPE_INDEX, Subscriber::SUBSCRIBE_TYPE_TEST, Subscriber::SUBSCRIBE_TYPE_MANUAL
                ])
                ->get(['email'])->toArray();
            /* 获取退订用户 */
            $unSubscribers = Subscriber::query()->where('unsubscribe', '=', Subscriber::STATUS_UNSUBSCRIBE)->get(['email'])->toArray();

            /* 退订邮箱 */
            $unSubscriberEmails = array_column($unSubscribers, 'email');

            /* 最终发送邮箱 */
            $emails = array_column($subscribers, 'email');

            /* 从其余二者中提取 email 属性，同时剔除退订的邮箱 */
            foreach ($publishers as $publisher) {
                if (!in_array($publisher['email'], $unSubscriberEmails, true)) {
                    $emails[] = $publisher['email'];
                }
            }
            foreach ($contacts as $contact) {
                if (!in_array($contact['email'], $unSubscriberEmails, true)) {
                    $emails[] = $contact['email'];
                }
            }

            $posts['send_email'] = Posts::EMAIL_SEND;
        } else {
            $emails = $request->input('email_list', []);
        }

//        return $this->jsonResponse($emails);

        /* 更新 Post 发送数据，send_email_subscriber 已废弃 */
        $posts['send_email_success'] = count($emails);
        $posts['send_email_subscriber'] = count($emails);
        $posts->save();

        /* 为所有邮箱生成对应的邮件视图模型 */
        $postsList = PostsService::generateEmailViewObjects($posts, $emails);

        foreach ($postsList as $email => $item) {
            /* 队列发送，在开发或修改此功能时请保持注释状态，逐一验证其他数据，正式发版后再取消注释 */
            Mail::to(['email' => $email])->queue(new EdmEmail($item));
        }

        return $this->jsonResponse();
    }

    /**
     * 将邮件发送给测试用户组
     * @param $id
     * @return JsonResponse
     */
    public function testEdmEmail($id): JsonResponse {
        $posts = Posts::query()->find($id);
        if ($posts === null || !$posts instanceof Posts) {
            return $this->jsonResponse(['id' => 'Post id not found'], 16001);
        }

        /* 获取测试邮箱列表 */
        $testSubscribers = Subscriber::query()
            ->whereIn('subscribe_type', [Subscriber::SUBSCRIBE_TYPE_TEST])
            ->where('unsubscribe', Subscriber::STATUS_SUBSCRIBE)
            ->where('language', '=', $posts['language'])
            ->get()->toArray();

        $postsList = PostsService::generateEmailViewObjects($posts, array_column($testSubscribers, 'email'));

        foreach ($postsList as $email => $item) {
            Mail::to(['email' => $email])->queue(new EdmEmail($item));
        }

        return $this->jsonResponse(['emails' => array_column($testSubscribers, 'email')], 0);
    }

    public function emailView($id)
    {
        $posts = Posts::query()->find($id);

        if ($posts == null || !$posts instanceof Posts) {
            return "FALSE";
        }

        $postsList = PostsService::generateEmailViewObjects($posts, ['zoharyips@outlook.com']);

        return view('emails.edm')->with('posts', $postsList['zoharyips@outlook.com']);
    }
}
