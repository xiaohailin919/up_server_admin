<?php

namespace App\Jobs;

use App\Models\MySql\Posts;
use App\Models\MySql\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscribeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $post_id;
    protected $post;

    /**
     * Create a new job instance.
     * @param string $email
     * @param int $post_id
     */
    public function __construct(string $email, int $post_id)
    {
        $this->email = $email;
        $this->post_id = $post_id;
        $this->post = Posts::query()->where('id', $post_id)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $postCache = Cache::get(Subscriber::POST_CONTENT_KEY . $this->post_id);
        if (empty($postCache) || empty($postCache['id']) || empty($postCache['title']) || empty($postCache['content'])) {
            Log::info($postCache == null ? 'Get sending email post cache failed.' : 'post id/title/content is missing');
            return;
        }
        /* 生成邮件签名 */
        $signature = md5(Subscriber::UNSUBSCRIBE_TOKEN . '@@@' . $this->email . '$$$' . $this->post_id);
        Mail::send('emails.post', ['event' => $postCache, 'email' => $this->email, 'signature' => $signature], function($message) {
            $message->to($this->email)->subject($this->post['title']);
        });

        /* 成功记数：failure 函数返回一个数组，每个数组成员表示发送失败的个数，若为 0 则表示所有 send 函数中的收件人发送成功 */
        if (count(Mail::failures()) == 0) {
            $this->post['send_email_success'] += 1;
            $this->post->save();
        }
        Log::info('Send Email to ' . $this->email . count(Mail::failures()) == 0 ? ' success!' : ' failed.');
    }
}
