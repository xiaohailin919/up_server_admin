<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/11/28
 * Time: 11:35
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MySql\Publisher;
use App\Services\Mail as MailService;

class Mail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:service {mode=latest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command mail service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 测试环境计划任务不跑
//        if (!empty($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'test') {
//            echo 'test is stop';
//            return;
//        }
        $mode = $this->argument('mode');
        if ($mode == 'remind_reg') {
            MailService::toRemindRegEmail();
        } else if ($mode == 'remind_info') {
            MailService::toRemindInfo();
        } else if($mode == "pending_placement"){
            MailService::remindPendingPlacement();
        } else if($mode == 'publisher-active') {
            // 开发者激活成功邮件
            $this->publisherActive();
        } else {
            echo "mode is error";
        }
    }

    /**
     * 开发者激活成功邮件
     * @return bool
     */
    private function publisherActive(){
        $time = strtotime('-1 day');
        $publisher = Publisher::where('create_time', '>', $time)
            ->where('status', Publisher::STATUS_RUNNING)
            ->where('sub_account_parent', 0)
            ->where('check_mail_status', 0)
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->toArray();

        foreach ($publisher as $val) {
            MailService::toPublisherEmail($val);
        }

        return true;
    }
}
