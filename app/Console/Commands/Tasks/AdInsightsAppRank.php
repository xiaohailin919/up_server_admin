<?php

namespace App\Console\Commands\Tasks;

use App\Models\Mongo\AdInsightsAppRank as Model;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Log;
use Storage;

class AdInsightsAppRank extends Command
{

    protected $signature = 'task:app-rank';

    protected $description = '用于爬取热云数据 App 排行榜';

    private $httpClient;
    private $httpOption;

    const URL_LOGIN    = 'https://www.adinsights.cn/adi/api/ins/login';
    const URL_DATE_OPT = 'https://www.adinsights.cn/adi/api/v3/common/options/date_type_options';
    const URL_LIST     = 'https://www.adinsights.cn/adi/api/v3/insight/product/list';
    const URL_DETAIL   = 'https://www.adinsights.cn/adi/api/v3/insight/product/detail';

    const USERNAME = 'yue@magicstudio.online';
    const PASSWORD = 'mobpower123';

    const UA_ANDROID = '1';
    const UA_IOS = '2';

    const PAGE_SIZE_LIMIT = 20;

    private $andAllParam = [
        'uaIds'     => self::UA_ANDROID,
        'dataRange' => "ALL",
        'pageSize'  => 150,
    ];

    private $andNewParam = [
        'uaIds'     => self::UA_ANDROID,
        'dataRange' => "NEW",
        'pageSize'  => 100,
    ];

    private $iosAllParam = [
        'uaIds'     => self::UA_IOS,
        'dataRange' => "ALL",
        'pageSize'  => 150,
    ];

    private $iosNewParam = [
        'uaIds'     => self::UA_IOS,
        'dataRange' => "NEW",
        'pageSize'  => 100,
    ];

    public function handle()
    {
        Log::info(__METHOD__ . ': Ad Insights app rank list crawling job start...');

        $this->httpClient = new Client();
        $this->httpOption = [
            RequestOptions::JSON => [
                'email'     => self::USERNAME,
                'password'  => self::PASSWORD,
                'isrember'  => false,
                'autologin' => false,
                'source'    => 1
            ],
            RequestOptions::TIMEOUT => 30
        ];

        $token = $this->getToken();

//        $token = '4041952a-fdb1-4489-889d-b853f1314b2c';

        $this->httpOption[RequestOptions::COOKIES] = CookieJar::fromArray(['TOKEN' => $token], 'www.adinsights.cn');

        $this->info(__METHOD__ . ': TOKEN set[' . $token . '], start crawling...');

        $commonParam = [
            'catIds'        => '',
            'tagIds'        => '',
            'mediaIds'      => '',
            'sortField'     => 'MATERIAL',
            'sortType'      => 'DESC',
            'keyword'       => '',
            'dateTypeId'    => $this->getWeekDateType(),
            'dataType'      => 'GAME',
            'adfactionsIds' => '',
            'pageIndex'     => 1,
            'mediaTypeId'   => -1,
        ];

        $andAllList = $this->getRankList(array_merge($commonParam, $this->andAllParam));
        $andNewList = $this->getRankList(array_merge($commonParam, $this->andNewParam));
        $iosAllList = $this->getRankList(array_merge($commonParam, $this->iosAllParam));
        $iosNewList = $this->getRankList(array_merge($commonParam, $this->iosNewParam));

        $andAllList = $this->parseData($andAllList);
        $andNewList = $this->parseData($andNewList);
        $iosAllList = $this->parseData($iosAllList);
        $iosNewList = $this->parseData($iosNewList);

        $andAllList = $this->appendDetail($andAllList, self::UA_ANDROID);
        $andNewList = $this->appendDetail($andNewList, self::UA_ANDROID);
        $iosAllList = $this->appendDetail($iosAllList, self::UA_IOS);
        $iosNewList = $this->appendDetail($iosNewList, self::UA_IOS);

//        $this->persistentDataJSON($andAllList, 'android_all');
//        $this->persistentDataJSON($andNewList, 'android_new');
//        $this->persistentDataJSON($iosAllList, 'ios_all');
//        $this->persistentDataJSON($iosNewList, 'ios_new');

        $data = [
            [
                'platform'   => Model::PLATFORM_ANDROID,
                'data_range' => Model::DATA_RANGE_ALL,
                'list'       => $andAllList,
            ],[
                'platform'   => Model::PLATFORM_ANDROID,
                'data_range' => Model::DATA_RANGE_NEW,
                'list'       => $andNewList,
            ],[
                'platform'   => Model::PLATFORM_IOS,
                'data_range' => Model::DATA_RANGE_ALL,
                'list'       => $iosAllList,
            ],[
                'platform'   => Model::PLATFORM_IOS,
                'data_range' => Model::DATA_RANGE_NEW,
                'list'       => $iosNewList,
            ],
        ];

        foreach ($data as $datum) {
            Model::query()->updateOrInsert([
                'date'       => date('Ymd'),
                'platform'   => $datum['platform'],
                'data_range' => $datum['data_range']
            ], [
                'list' => $datum['list'],
            ]);
        }
    }

    private function getToken()
    {
        $response = $this->httpClient->post(self::URL_LOGIN, $this->httpOption);

        $responseData = json_decode($response->getBody(), true);

        $token = $responseData['content']['TOKEN'];

        $this->info(__METHOD__ . ': TOKEN gotten [' . $token . ']');

        return $token;
    }

    /**
     * 获取最近七天的那个枚举
     *
     * @return mixed|string
     */
    private function getWeekDateType()
    {
        $this->httpOption[RequestOptions::JSON] = ['isToDay' => 1];
        $response = $this->httpClient->post(self::URL_DATE_OPT, $this->httpOption);

        $responseData = json_decode($response->getBody(), true);

        $list = $responseData['按日'] ?? [['name' => '近7日', 'value' => '17']];

        foreach ($list as $item) {
            if ($item['name'] == '近7日') {
                return $item['value'];
            }
        }

        return '17';
    }

    private function getRankList($customParam)
    {
        $defaultParam = [
            'catIds'        => '',
            'tagIds'        => '',
            'mediaIds'      => '',
            'sortField'     => 'MATERIAL',
            'sortType'      => 'DESC',
            'keyword'       => '',
            'dateTypeId'    => $this->getWeekDateType(),
            'dataType'      => 'GAME',
            'dataRange'     => 'ALL',
            'uaIds'         => self::UA_ANDROID,
            'adfactionsIds' => '',
            'pageIndex'     => 1,
            'pageSize'      => 20,
            'mediaTypeId'   => -1,
        ];

        $param = array_merge($defaultParam, $customParam);

        $requiredSize = $param['pageSize'];
        $param['pageSize'] = $param['pageSize'] > self::PAGE_SIZE_LIMIT ? self::PAGE_SIZE_LIMIT : $param['pageSize'];
        $requiredPage = ceil($requiredSize / $param['pageSize']);

        /* 第一次爬取获取所有数据元信息，如数量、最后更新时间等信息 */
        $this->httpOption[RequestOptions::JSON] = $param;
        $response = $this->httpClient->post(self::URL_LIST, $this->httpOption);

        $responseData = json_decode($response->getBody(), true);

        $pageModel  = $responseData['content']['pageModel'];
        $updateTime = $responseData['content']['lastUpdate'];
        $totalPage  = $pageModel['totalPage'];
        $dataList   = $pageModel['result'];

        /* 拉取数据直到达到需要的数据量，或者达到接口返回最大数据量 */
        for ($pageNo = 2, $maxPage = min($totalPage, $requiredPage); $pageNo <= $maxPage; $pageNo++) {

            $this->info(
                __METHOD__
                . ': Getting '
                . ($param['uaIds'] == self::UA_ANDROID ? 'android ' : 'ios ')
                . $param['dataRange']
                . ' list data ' . $pageNo . '/' . $maxPage
            );

            $param['pageIndex'] = $pageNo;
            $this->httpOption[RequestOptions::JSON] = $param;
            $response = $this->httpClient->post(self::URL_LIST, $this->httpOption);
            $responseData = json_decode($response->getBody(), true);

            /* 如果刚好更新了，重新拉 */
            if ($updateTime != $responseData['content']['lastUpdate']) {
                return $this->getRankList($customParam);
            }

            /* 最后一次多出来的数据舍弃掉 */
            $newList = $responseData['content']['pageModel']['result'];
            for ($i = 0, $iMax = min($requiredSize - count($dataList), count($newList)); $i < $iMax; $i++) {
                $dataList[] = $newList[$i];
            }
        }

        /* 处理所有数据 */
        foreach ($dataList as $idx => $datum) {
            $dataList[$idx]['data_range'] = $param['dataRange']; // 1：所有产品，2：新产品
        }

        return $dataList;
    }

    private function parseData($dataList)
    {
        /* 字段名映射列表，将对方的字段名映射为自家风格的字段名，映射表中没有的字段予以舍弃 */
        $fieldMap = [
            'productId'       => ['name' => 'id',           'fields' => []], // AdInsight 中的 ID
            'productName'     => ['name' => 'name',         'fields' => []], // 产品名称
            'productIcon'     => ['name' => 'icon_url',     'fields' => []], // 产品图标链接
            'catName'         => ['name' => 'category',     'fields' => []], // 应用分类
            'tagName'         => ['name' => 'tag',          'fields' => []], // 应用标签
            'companyName'     => ['name' => 'company',      'fields' => []], // 公司名称
            'mediaTypeId'     => ['name' => 'media_type',   'fields' => []], // 产品类型【1：APP，2：小游戏，-1：所有】
            'data_range'      => ['name' => 'data_range',   'fields' => []],
            'lifeCycle'       => ['name' => 'life_cycle',   'fields' => []], // 投放生命周期，从第一次投放到最后一次投放的时间
            'resNum'          => ['name' => 'material_num', 'fields' => []], // 总投放素材数
            'mateNum'         => ['name' => 'mate_count',   'fields' => []], // 总投放创意组数
            'dayNum'          => ['name' => 'day_num',      'fields' => []], // 总投放天数，非持续天数
            'mediaCount'      => ['name' => 'media_count',  'fields' => []], // 总媒体数
            'adFactions'      => ['name' => 'faction_list', 'fields' => [    // 平台列表
                'name' => 'name',
                'mcnt' => 'mate_count'
            ]],
            'medias'          => ['name' => 'media_list',      'fields' => [    // 媒体列表
                'name' => 'name',
                'mcnt' => 'mate_count'
            ]],
            'opCompanyNum'    => ['name' => 'op_company_num',  'fields' => []], // 联运公司数
            'opCompanys'      => ['name' => 'op_company_list', 'fields' => [    // 联运公司列表
                'name' => 'name',
                'mcnt' => 'mate_count'
            ]],
            'uas'             => ['name' => 'platform_list',   'fields' => [    // 系统列表
                'name' => 'name',
                'mcnt' => 'mate_count'
            ]],
            'datas'           => ['name' => 'statistics_list', 'fields' => [    // 日统计数据
                'mcnt'      => 'mate_count',
                'viewTimes' => 'view_times',
                'name'      => 'date'
            ]],
        ];

        foreach ($dataList as $idx => $datum) {

            /* 换字段名，同时舍弃无用字段 */
            $tmp = [];
            foreach ($fieldMap as $key => $val) {
                if (!empty($val['fields'])) {
                    foreach ($datum[$key] as $item) {
                        $tmpItem = [];
                        foreach ($val['fields'] as $innerKey => $innerVal) {
                            $tmpItem[$innerVal] = $item[$innerKey];
                        }
                        $tmp[$val['name']][] = $tmpItem;
                    }
                } else {
                    $tmp[$val['name']] = $datum[$key];
                }
            }

            /* 修改字段值 */
            $tmp['company'] = $tmp['company'] ?? '-';
            $tmp['icon_url'] = 'http://img.adinsights.cn/static/' . $tmp['icon_url'];

            $dataList[$idx] = $tmp;
        }

        return $dataList;
    }

    private function appendDetail($appList, $ua = self::UA_ANDROID)
    {
        $client = new Client();
        $this->httpOption[RequestOptions::JSON] = ['dataType' => 'GAME'];

        $all = count($appList);

        foreach ($appList as $idx => $datum) {

            if ($idx % 15 == 0) {
                $this->info(__METHOD__ . ': Getting App Details ' . $idx . '/' . $all);
            }

            $this->httpOption[RequestOptions::JSON]['productId'] = $datum['id'];

            $response = $client->post(self::URL_DETAIL, $this->httpOption);

            $responseData = json_decode($response->getBody(), true);

            $content = $responseData['content'];

            $appList[$idx]['ios_url'] = $content['iosDownloadUrl'] ?? '';
            $appList[$idx]['and_url'] = $content['androidDownloadUrl'] ?? '';
            $appList[$idx]['download_url'] = $ua == self::UA_ANDROID ? $appList[$idx]['and_url'] : $appList[$idx]['ios_url'];
        }

        return $appList;
    }

    /**
     * 持久化数据，保存至 JSON 文件
     *
     * @param $appList
     * @param string $suffix
     */
    private function persistentDataJSON($appList, $suffix = '')
    {
        $fileName = $suffix == '' ? 'ad_insight_' . date('Ymd') . '.json' : 'ad_insight_' . date('Ymd_') . $suffix . '.json';

        $this->info(__METHOD__ . ': Persisting data ' . $suffix);

        Storage::disk('local')->put($fileName, json_encode($appList, JSON_PRETTY_PRINT));
    }
}
