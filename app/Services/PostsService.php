<?php


namespace App\Services;


use App\Helpers\ArrayUtil;
use App\Helpers\Upload;
use App\Models\MySql\Posts;
use App\Models\MySql\PostsRelationship;
use App\Models\MySql\PostsTerm;
use App\Models\MySql\Subscriber;
use App\Models\MySql\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsService {

    /**
     * 筛选与修改 Posts 列表数据
     *
     * @param $data
     * @return mixed
     */
    public static function filterListData($data) {

        /* 查找所有 PostsTerm 与 Posts 的关系 */
        $relationships = PostsRelationship::query()->from('posts_relationship as t1')
            ->leftJoin('posts_term as t2', 't1.term_id', '=', 't2.id')
            ->where('t2.status', '=', PostsTerm::STATUS_ACTIVE)
            ->get(['t1.posts_id', 't1.term_id', 't2.name', 't2.slug', 't2.type'])->toArray();

        /* 先转化成映射表，不要在遍历 Posts 列表的时候去生成，不然性能极低 */
        $postIdCategoryListMap = [];
        $postIdTagListMap = [];
        foreach ($relationships as $relationship) {
            /* 准备好新纪录，减少代码重复 */
            $newRecord = [
                'id' => $relationship['term_id'],
                'name' => $relationship['name'],
                'slug' => $relationship['slug'],
            ];

            if ($relationship['type'] == PostsTerm::TYPE_CATEGORY) {
                /* 如果已经有了的话，直接加入列表 */
                if (array_key_exists($relationship['posts_id'], $postIdCategoryListMap)) {
                    $postIdCategoryListMap[$relationship['posts_id']][] = $newRecord;
                } else {
                    $postIdCategoryListMap[$relationship['posts_id']] = [$newRecord];
                }
            } else {
                /* 如果已经有了的话，直接加入列表 */
                if (array_key_exists($relationship['posts_id'], $postIdTagListMap)) {
                    $postIdTagListMap[$relationship['posts_id']][] = $newRecord;
                } else {
                    $postIdTagListMap[$relationship['posts_id']] = [$newRecord];
                }
            }
        }

        foreach ($data as $index => $datum) {
            $newDatum = [];

            $newDatum['id']                     = $datum['id'] ?? 0;
            $newDatum['title']                  = $datum['title'] ?? '';
            $newDatum['type']                   = $datum['type'] ?? Posts::TYPE_NEWS;
            $newDatum['language']               = $datum['language'] ?? Posts::LANGUAGE_CHINESE;
            $newDatum['content']                = $datum['content'] ?? '';
            $newDatum['thumbnail']              = $datum['thumbnail'] == '' ? '' : Upload::getUrlBySavePath($datum['thumbnail']);
            $newDatum['source']                 = $datum['source'] ?? '';
            $newDatum['popular']                = $datum['popular'] ?? Posts::POPULAR_NO;
            $newDatum['views']                  = $datum['views'] ?? 0;
            $newDatum['rank']                   = $datum['rank'] ?? 0;
            $newDatum['status']                 = $datum['status'] ?? Posts::STATUS_DRAFT;
            $newDatum['send_email']             = $datum['send_email'] ?? Posts::EMAIL_NOT;
            $newDatum['send_email_subscriber']  = $datum['send_email_subscriber'] ?? 0;
            $newDatum['send_email_success']     = $datum['send_email_success'] ?? 0;
            $newDatum['send_email_open']        = $datum['send_email_open'] ?? 0;
            $newDatum['send_email_redirect']    = $datum['send_email_redirect'] ?? 0;
            $newDatum['send_email_unsubscribe'] = $datum['send_email_unsubscribe'] ?? 0;
            $newDatum['post_time']              = $datum['post_time'] < 0 ? 0 : $datum['post_time'];
            $newDatum['create_time']            = $datum['create_time'] < 0 ? 0 : $datum['create_time'];
            $newDatum['update_time']            = $datum['update_time'] < 0 ? 0 : $datum['update_time'];
            $newDatum['admin_id']               = $datum['admin_id'] ?? 0;
            $newDatum['admin_name']             = Users::getName($datum['admin_id']);

            /* News 类型数据转化 */
            $newDatum['news_description']      = $datum['type'] == Posts::TYPE_NEWS ? $datum['description'] : '';
            $newDatum['news_keyword_list']     = $datum['type'] == Posts::TYPE_NEWS ? ArrayUtil::explodeString($datum['keyword'], ',') : [];
            $newDatum['news_category_list']    = $datum['type'] == Posts::TYPE_NEWS ? $postIdCategoryListMap[$datum['id']] ?? [] : [];
            $newDatum['news_tag_list']         = $datum['type'] == Posts::TYPE_NEWS ? $postIdTagListMap[$datum['id']] ?? [] : [];

            /* Event 类型数据转化 */
            $newDatum['event_location']        = $datum['type'] == Posts::TYPE_EVENT ? $datum['location'] : '';
            $newDatum['event_time']            = $datum['type'] == Posts::TYPE_EVENT && $datum['event_time'] > 0 ? $datum['event_time'] : 0;

            /* Report 类型数据转化 */
            $newDatum['report_information']    = $datum['type'] == Posts::TYPE_REPORT ? $datum['description'] : '';
            $newDatum['report_download_url']   = $datum['type'] == Posts::TYPE_REPORT ? $datum['redirect_url'] : '';

            /* Email 类型数据转化 */
            $newDatum['email_btn_text']        = $datum['type'] == Posts::TYPE_EMAIL ? $datum['description'] : '';
            $newDatum['email_redirect_url']    = $datum['type'] == Posts::TYPE_EMAIL ? $datum['redirect_url'] : '';

            /* Email 类型置空这两项 */
            if ($newDatum['type'] == Posts::TYPE_EMAIL) {
                $newDatum['rank'] = '';
                $newDatum['popular'] = '';
            }

            $data[$index] = $newDatum;
        }

        return $data;
    }

    /**
     * 生成导出列表头部
     *
     * @return array
     */
    public static function generateExportHeader(): array
    {
        return [
            'id'                     => __('common.id'),
            'type'                   => __('common.posts.type'),
            'language'               => __('common.posts.language'),
            'title'                  => __('common.posts.title'),
            'description'            => __('common.posts.description'),
            'post_time'              => __('common.posts.post_time'),
            'thumbnail'              => __('common.posts.thumbnail_url'),
            'keyword'                => __('common.posts.keyword'),
            'source'                 => __('common.posts.source'),
            'popular'                => __('common.posts.popular'),
            'views'                  => __('common.posts.views'),
            'rank'                   => __('common.posts.rank'),
            'event_location'         => __('common.posts.event_location'),
            'event_time'             => __('common.posts.event_time'),
            'redirect_url'           => __('common.posts.redirect_url'),
            'send_email'             => __('common.posts.send_email'),
            'admin_name'             => __('common.posts.admin_name'),
            'create_time'            => __('common.create_time'),
            'update_time'            => __('common.update_time'),
            'send_email_success'     => __('common.posts.send_email_success'),
            'send_email_open'        => __('common.posts.send_email_open'),
            'send_email_redirect'    => __('common.posts.send_email_redirect'),
            'send_email_unsubscribe' => __('common.posts.send_email_unsubscribe'),
        ];
    }

    /**
     * 生成导出报表的数据
     * 注意：该数据应该是从数据库查出来的裸数据，不应该修饰或者过滤过
     *
     * @param array $data
     * @return array
     */
    public static function generateExportData($data) {

        $res = [];
        foreach ($data as $datum) {

            $tmp['id']                     = $datum['id'];
            $tmp['type']                   = Posts::getTypeName($datum['type']);
            $tmp['language']               = Posts::getLanguageName($datum['language']);
            $tmp['title']                  = $datum['title'];
            $tmp['description']            = $datum['description'];
            $tmp['post_time']              = date('Y-m-d H:i:s', $datum['post_time'] / 1000);
            $tmp['thumbnail']              = $datum['thumbnail'];
            $tmp['keyword']                = $datum['keyword'];
            $tmp['source']                 = $datum['source'];
            $tmp['popular']                = $datum['popular'] == Posts::POPULAR_YES ? __('common.no') : __('common.yes');
            $tmp['views']                  = $datum['views'];
            $tmp['rank']                   = $datum['rank'];
            $tmp['event_location']         = $datum['location'];
            $tmp['event_time']             = date('Y-m-d H:i:s', $datum['event_time'] / 1000);
            $tmp['redirect_url']           = $datum['redirect_url'];
            $tmp['send_email']             = Posts::getEmailStatusName($datum['send_email']);
            $tmp['admin_name']             = $userIdNameMap[$datum['admin_id']] ?? '';
            $tmp['create_time']            = date('Y-m-d H:i:s', $datum['create_time'] / 1000);
            $tmp['update_time']            = date('Y-m-d H:i:s', $datum['update_time'] / 1000);
            $tmp['status']                 = Posts::getStatusName($datum['status']);
            $tmp['send_email_success']     = $datum['send_email_success'];
            $tmp['send_email_open']        = $datum['send_email_open'];
            $tmp['send_email_redirect']    = $datum['send_email_redirect'];
            $tmp['send_email_unsubscribe'] = $datum['send_email_unsubscribe'];
            $res[] = $tmp;
        }

        return $res;
    }

    /**
     * 生成邮件视图对象
     * 注：由于使用队列发送邮件，Posts 对象在序列化时会丢失该对象原本没有或修改的数据，因此使用数组传递
     *
     * @param Posts $posts
     * @param $emails
     * @return array
     */
    public static function generateEmailViewObjects(Posts $posts, $emails): array
    {
        /* 构造通用数据 */
        $tmp = [];
        $tmp['id']          = $posts['id'];
        $tmp['language']    = $posts['language'];
        $tmp['title']       = $posts['title'];
        $tmp['type']        = $posts['type'];
        $tmp['content']     = $posts['content'];
        $tmp['thumbnail']   = Upload::getUrlBySavePath($posts['thumbnail']);
        $tmp['btn_text']    = $posts['language'] == Posts::LANGUAGE_CHINESE ? '一键获取' : 'Get Now';

        if ($tmp['type'] == Posts::TYPE_NEWS) {
            /* 去除所有 HTML 标签 */
            $tmp['content'] = strip_tags($tmp['content']);
            /* 去除所有 &nbsp; */
            $tmp['content'] = str_replace(["&nbsp;", "\t"], "", $tmp['content']);
            /* 将所有重复的换行转为单个换行 */
            $tmp['content'] = str_replace(["\r\n", "\n\n"], "\n", $tmp['content']);
            $tmp['content'] = str_replace("\n\n", "\n", $tmp['content']);
            $postContent = '';
            $contents = explode("\n", $tmp['content']);
            for ($i = 0, $iMax = count($contents) > 5 ? 5 : count($contents); $i < $iMax; $i++) {
                $postContent .= $contents[$i] . '<br/>';
            }
            $tmp['content'] = $postContent;
            if (count($contents) > 5) {
                $tmp['content'] .= '...';
            }

            $tmp['redirect_url'] = "https://www.toponad.com/posts/" . $tmp['id'] . ".html";
        } else {
            $tmp['redirect_url'] = $posts['redirect_url'];
        }

        if ($tmp['type'] == Posts::TYPE_EMAIL) {
            $tmp['btn_text'] = $posts['description'];
        }

        /* 构造结果集合，补充差异数据 */
        $res = [];
        foreach ($emails as $email) {
            /* 退订地址信息 */
            $signature = md5(Subscriber::UNSUBSCRIBE_TOKEN . '@@@' . $email . '$$$' . $tmp['id']);
            /* EDM 邮件打开埋点信息 */
            $tmp['bp_open_url'] = env('DN_UP_APP') . 'a/email/open.jpg?_e=' . base64_encode($email) . '&_p=' . base64_encode($tmp['id']);
            /* EDM 邮件跳转埋点 */
            $tmp['bp_redirect_url'] = env('DN_UP_APP') . 'a/email/redirect?_d=' . base64_encode($tmp['redirect_url']) . '&_e=' . base64_encode($email) . '&_p=' . base64_encode($tmp['id']);
            /* EDM 邮件退订埋点 */
            $tmp['bp_unsubscribe_url'] = env('DN_UP_APP') . 'a/email/unsubscribe?email=' . $email . '&post=' . $tmp['id'] . '&_s=' . $signature;
            $res[$email] = $tmp;
        }

        return $res;
    }

    /**
     * @param array $news
     * @param Request $request
     * @throws Exception
     */
    public static function storeNews(array $news, Request $request)
    {
        $news['description'] = $request->input('news_description', '');
        $news['keyword'] = implode(',', $request->input('news_keyword_list', []));

        $categories = $request->input('news_category_list', []);
        $tags = $request->input('news_tag_list', []);

        try {
            DB::beginTransaction();
            $id = Posts::query()->insertGetId($news);
            foreach ($categories as $category) {
                PostsRelationship::query()->create(['posts_id' => $id, 'term_id' => $category,]);
            }
            foreach ($tags as $tag) {
                PostsRelationship::query()->create(['posts_id' => $id, 'term_id' => $tag,]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function storeEvent(array $event, Request $request)
    {
        $event['location'] = $request->input('event_location', '');
        $event['event_time'] = date('Y-m-d H:i:s', $request->input('event_time', time() * 1000) / 1000);
        Posts::query()->create($event);
    }

    public static function storeReport(array $report, Request $request)
    {
        $report['description'] = $request->input('report_information', '');
        $report['redirect_url'] = $request->input('report_download_url', '');
        Posts::query()->create($report);
    }

    public static function storeEmail(array $email, Request $request)
    {
        $email['rank'] = 0;
        $email['popular'] = Posts::POPULAR_NO;
        $email['post_time'] = date('Y-m-d H:i:s');
        $email['description'] = $request->input('email_btn_text', '');
        $email['redirect_url'] = $request->input('email_redirect_url', '');
        Posts::query()->create($email);
    }

    /**
     * @param Posts $news
     * @param Request $request
     * @throws Exception
     */
    public static function updateNews(Posts $news, Request $request)
    {
        /* 描述 */
        $news['description'] = $request->input('news_description', '');

        /* 关键词 */
        $news['keyword'] = implode(',', $request->input('news_keyword_list', []));

        $categories = $request->input('news_category_list', []);
        $tags = $request->input('news_tag_list', []);
        $relationships = PostsRelationship::query()->where('posts_id', $news['id'])->get();

        try {
            DB::beginTransaction();
            $news->save();
            foreach ($relationships as $relationship) {
                $relationship->delete();
            }
            foreach ($categories as $category) {
                PostsRelationship::query()->create(['posts_id' => $news['id'], 'term_id' => $category,]);
            }
            foreach ($tags as $tag) {
                PostsRelationship::query()->create(['posts_id' => $news['id'], 'term_id' => $tag,]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function updateEvent(Posts $event, Request $request)
    {
        $event['location'] = $request->input('event_location', '');
        $event['event_time'] = date('Y-m-d H:i:s', $request->input('event_time', time() * 1000) / 1000);
        $event->save();
    }

    public static function updateReport(Posts $report, Request $request)
    {
        $report['description'] = $request->input('report_information', '');
        $report['redirect_url'] = $request->input('report_download_url', '');
        $report->save();
    }

    public static function updateEmail(Posts $email, Request $request)
    {
        $email['rank'] = 0;
        $email['popular'] = Posts::POPULAR_NO;
        $email['post_time'] = date('Y-m-d H:i:s');
        $email['description'] = $request->input('email_btn_text');
        $email['redirect_url'] = $request->input('email_redirect_url');
        $email->save();
    }
}