<?php

namespace App\Console\Commands;

use App\Models\MySql\Posts;
use App\Models\MySql\Subscriber;
use App\Services\PostsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendSubscribeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        /* 获取待发送文章 */
        $post = Posts::query()->where('send_email', Posts::EMAIL_SENDING)->first();

        if ($post == null || !$post instanceof Posts) {
            Log::info('No email needs to be sent.');
            return;
        }
        Log::info('A subscribe email needs to be sent: id ' . $post['id'] . ' | title ' . $post['title']);

        /* 更新 send_email_subscriber 字段 */
        $post['send_email_subscriber'] = Subscriber::query()->where('unsubscribe', Subscriber::STATUS_SUBSCRIBE)->count();
        $post->save();

        $post = PostsService::generateEmailViewObject($post);

        /* 将文章内容和发送成功数放入缓存 */
        Cache::forget(Subscriber::POST_CON1ENT_KEY . $post['id']);
        Cache::put(Subscriber::POST_CONTENT_KEY . $post['id'], $post, 600);

        /* 获取订阅者邮箱，投递至队列 */
        $subscribers = Subscriber::query()->where('unsubscribe', Subscriber::STATUS_SUBSCRIBE)->get();
        foreach ($subscribers as $subscriber) {
            /* 判断订阅者是否订阅首页文章 */
            if ($subscriber['subscribe_type'] == Subscriber::SUBSCRIBE_TYPE_ALL
                || $subscriber['subscribe_type'] == Subscriber::SUBSCRIBE_TYPE_INDEX) {
                $this->info($subscriber['email']);
                \App\Jobs\SendSubscribeEmail::dispatch($subscriber['email'], $post['id']);
            }
        }
    }
}
