<?php

namespace App\Console\Commands\Emails;

use App\Models\Mongo\AdInsightsAppRank as Model;
use App\Mail\AdInsightsAppRank         as Email;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Log;

class AdInsightsAppRank extends Command
{
    protected $signature = 'email:app-rank';

    protected $description = 'Command description';

    private $receiver = [
        'bdam@toponad.com',
        'jeff@toponad.com',
//        'zoharyips@outlook.com',
//        '1706200027@e.gzhu.edu.cn',
    ];

    public function handle()
    {
        $this->info(__METHOD__ . ': Getting app rank list');
        $appListToday = Model::query()->where('date', date('Ymd'))->get()->toArray();
        $this->info(__METHOD__ . ': App rank list gotten');

        $iosAllList = array_first($appListToday, static function($value) {
            return $value['platform'] == Model::PLATFORM_IOS && $value['data_range'] == Model::DATA_RANGE_ALL;
        })['list'];
        $iosNewList = array_first($appListToday, static function($value) {
            return $value['platform'] == Model::PLATFORM_IOS && $value['data_range'] == Model::DATA_RANGE_NEW;
        })['list'];
        $andAllList = array_first($appListToday, static function($value) {
            return $value['platform'] == Model::PLATFORM_ANDROID && $value['data_range'] == Model::DATA_RANGE_ALL;
        })['list'];
        $andNewList = array_first($appListToday, static function($value) {
            return $value['platform'] == Model::PLATFORM_ANDROID && $value['data_range'] == Model::DATA_RANGE_NEW;
        })['list'];

        $data =[
            [
                'title'    => 'iOS 全部产品（Top 150）',
                'platform' => 'iOS',
                'list'     => $iosAllList
            ],[
                'title'    => 'iOS 新投放产品（Top 100）',
                'platform' => 'iOS',
                'list'     => $iosNewList
            ],[
                'title'    => 'Android 全部产品（Top 150）',
                'platform' => 'Android',
                'list'     => $andAllList
            ], [
                'title'    => 'Android 新投放产品（Top 100）',
                'platform' => 'Android',
                'list'     => $andNewList
            ],
        ];

        Mail::to($this->receiver)->send(new Email($data));

        Log::info(__METHOD__ . ': Send Ad Insights App Rank List success.');
    }
}
