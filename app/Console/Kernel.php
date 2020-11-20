<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Env::class,
        Commands\Publisher::class,
        Commands\App::class,
//        Commands\Placement::class,
        Commands\DictNetwork::class,
        Commands\SendSubscribeEmail::class,
        Commands\UpdateMetricsData::class,

        // monitors
        Commands\Monitors\AppStrategy::class,
        Commands\Monitors\PlacementStrategy::class,

        // Tasks
        Commands\Tasks\AdInsightsAppRank::class,

        // Email
        Commands\Emails\AdInsightsAppRank::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 增量同步
        $schedule->command('sync:publisher')
            ->cron('* * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('sync:app')
            ->cron('* * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 临时方案，每5分钟全量同步
        $schedule->command('sync:publisher', ['all'])
            ->cron('*/5 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 临时方案，每5分钟全量同步
        $schedule->command('sync:app', ['all'])
            ->cron('*/5 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 追加注册提示
        $schedule->command('mail:service', ['remind_reg'])
            ->cron('*/1 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 开发者激活成功邮件
        $schedule->command('mail:service', ['publisher-active'])
            ->cron('*/3 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 抓取人民币汇率
        $schedule->command('auto:exchange-rate', ['latest', 'CNY'])
            ->cron('0,1,2 0 * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        // 欧元汇率
        $schedule->command('auto:exchange-rate', ['latest', 'EUR'])
            ->cron('0,1,2 0 * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        // 英镑汇率
        $schedule->command('auto:exchange-rate', ['latest', 'GBP'])
            ->cron('0,1,2 0 * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        // 日元汇率
        $schedule->command('auto:exchange-rate', ['latest', 'JPY'])
            ->cron('0,1,2 0 * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        // 港币汇率
        $schedule->command('auto:exchange-rate', ['latest', 'HKD'])
            ->cron('0,1,2 0 * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 排序数据
        $schedule->command('data:metrics', ['--dimension=day'])
            ->cron('0 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('data:metrics', ['--dimension=week'])
            ->cron('3 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('data:metrics', ['--dimension=month'])
            ->cron('6 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('data:metrics', ['--dimension=day --type=placement'])
            ->cron('10 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('data:metrics', ['--dimension=week --type=placement'])
            ->cron('15 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        $schedule->command('data:metrics', ['--dimension=month --type=placement'])
            ->cron('20 4 * * *')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();

        // 监控
        $schedule->command('monitor:app-strategy')
            ->cron('*/10 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        $schedule->command('monitor:placement-strategy')
            ->cron('*/10 * * * * ')
            ->timezone('Asia/Shanghai')
            ->withoutOverlapping();
        
//        // Ad Insights App 排名
//        $schedule->command('task:app-rank')
//            ->cron('0 6 * * 1')
//            ->timezone('Asia/Shanghai')
//            ->withoutOverlapping();
//        $schedule->command('email:app-rank')
//            ->cron('0 9 * * 1')
//            ->timezone('Asia/Shanghai')
//            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
