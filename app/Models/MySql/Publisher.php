<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use App\Models\MySqlBase;
use App\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;

class Publisher extends MySqlBase
{
    protected $table = 'publisher';
    const TABLE = 'publisher';

    const STATUS_DELETED = 0;
    const STATUS_LOCKED  = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;
    const MODE_WHITE = 1;//白盒模式
    const MODE_BLACK = 2;//黑盒模式

    const TYPE_WHITE = 1;
    const TYPE_BLACK = 2;

    // 开发者等级
    const LEVEL_UNSET = 0;
    const LEVEL_S = 99;
    const LEVEL_A = 100;
    const LEVEL_B = 101;
    const LEVEL_C = 102;
    const LEVEL_D = 103;

    // Admin 后台数据权限
    const DATA_PERMISSION_OFF = 1;
    const DATA_PERMISSION_ON = 2;

    // API数据开关
    const API_SWITCH_OFF = 1;
    const API_SWITCH_ON  = 2;
    // 第三方报表导入开关
    const REPORT_IMPORT_SWITCH_OFF = 1;
    const REPORT_IMPORT_SWITCH_ON  = 2;
    // 设备维度数据开关
    const DEVICE_DATA_SWITCH_OFF = 1;
    const DEVICE_DATA_SWITCH_ON  = 2;
    // 用户版本开关状态
    const MIGRATE_STATUS_ORIGINAL = 1;
    const MIGRATE_STATUS_DOING    = 2;
    const MIGRATE_STATUS_FINISH   = 3;
    // 用户报表本币
    const CURRENCY_CNY = "CNY";
    const CURRENCY_USD = "USD";
    // Unit重复添加
    const UNIT_REPEAT_SWITCH_ON  = 2;
    const UNIT_REPEAT_SWITCH_OFF = 1;
    // MyOffer开关添加
    const MY_OFFER_SWITCH_ON  = 2;
    const MY_OFFER_SWITCH_OFF = 1;
    // 子账号
    const SUB_ACCOUNT_SWITCH_ON  = 2;
    const SUB_ACCOUNT_SWITCH_OFF = 1;
    // 子账号支持补扣量
    const DIST_SWITCH_ON  = 2;
    const DIST_SWITCH_OFF = 1;
    // Network 多账号
    const NETWORK_MULTIPLE_SWITCH_ON  = 2;
    const NETWORK_MULTIPLE_SWITCH_OFF = 1;
    // Report Timezone
    const REPORT_TIMEZONE_SWITCH_OFF = 1;
    const REPORT_TIMEZONE_SWITCH_ON  = 2;
    // 广告场景
    const SCENARIO_SWITCH_OFF = 1;
    const SCENARIO_SWITCH_ON  = 2;
    // ADX
    const ADX_SWITCH_OFF = 1;
    const ADX_SWITCH_ON  = 2;
    // ADX Unit
    const ADX_UNIT_SWITCH_OFF = 1;
    const ADX_UNIT_SWITCH_ON  = 2;

    // Publisher Channel
    const CHANNEL_TOPON = 0;
    const CHANNEL_233   = 10000;
    const CHANNEL_DLADS = 10001;

    // Publisher Channel Domain
    const CHANNEL_DOMAIN_TOPON = 'app.toponad.com';
    const CHANNEL_DOMAIN_233   = 'app.233.toponad.com';
    const CHANNEL_DOMAIN_DLADS = 'app.dlads.toponad.com';

    const DEFAULT_ALLOW_FIRMS = [
        1, 2, 3, 5, 6, 7, 8, 9, 10,
        11, 12, 13, 14, 15,
        21, 22, 23, 24, 25, 26, 28, 29,
        36, 37
    ];

    const SOURCE_OTHER           = 1;
    const SOURCE_MARKET_ACTIVITY = 2;
    const SOURCE_FRIENDS         = 3;
    const SOURCE_SEARCH_ENGINE   = 4;

    protected $hidden = [
        'password', 'salt', 'remember_token'
    ];

    protected $guarded = [
        'id'
    ];

    private static $idNameMap;

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function dataRoles(): MorphToMany
    {
        return $this->morphedByMany(
            DataRole::class,
            'target',
            'data_role_user_permission',
            'publisher_id',
            'target_id'
        )->withPivot('display_type');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'target',
            'data_role_user_permission',
            'publisher_id',
            'target_id'
        )->withPivot('display_type');
    }

    public function getMigrateStatusMap()
    {
        return [
            self::MIGRATE_STATUS_ORIGINAL => "老版本",
            self::MIGRATE_STATUS_DOING => "数据迁移中...",
            self::MIGRATE_STATUS_FINISH => "新版本",
        ];
    }

    public function getApiKey($publisherId)
    {
        if($publisherId <= 0){
            return '';
        }
        $publisher = $this->getOne(['api_key'], ['id' => $publisherId]);
        if(empty($publisher)){
            return '';
        }
        return $publisher['api_key'];
    }

    public static function getName($id) {
        if (self::$idNameMap == null) {
            $data = self::query()->get(['id', 'name'])->toArray();
            self::$idNameMap = array_column($data, 'name', 'id');
        }
        return $id == 0 ? 'All' : self::$idNameMap[$id] ?? '-';
    }
    
    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public function getStatusName($status)
    {
        $map = $this->getStatusMap(true);
        return $map[$status];
    }
    
    /**
     * 获取所有状态码映射配置
     *
     * @param boolean $deleted
     * @return array
     */
    public static function getStatusMap($deleted = false)
    {
        $map = [
            self::STATUS_DELETED => '已删除',
            self::STATUS_LOCKED  => '锁定',
            self::STATUS_PENDING => '待审核',
            self::STATUS_RUNNING => '正常',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }

    public static function getLevelMap(): array
    {
        return [
            self::LEVEL_UNSET => '',
            self::LEVEL_S => 'S',
            self::LEVEL_A => 'A',
            self::LEVEL_B => 'B',
            self::LEVEL_C => 'C',
            self::LEVEL_D => 'D',
        ];
    }

    public static function getLevelName($level): string
    {
        $map = self::getLevelMap();
        return $map[$level] ?? '-';
    }

    public function getApiSwitchMap()
    {
        return [
            self::API_SWITCH_OFF => '关闭',
            self::API_SWITCH_ON=> '打开',
        ];
    }

    public function getDeviceDataSwitchMap()
    {
        return [
            self::DEVICE_DATA_SWITCH_OFF => '关闭',
            self::DEVICE_DATA_SWITCH_ON => '打开',
        ];
    }

    public function getReportImportSwitchMap()
    {
        return [
            self::REPORT_IMPORT_SWITCH_OFF => '关闭',
            self::REPORT_IMPORT_SWITCH_ON => '打开',
        ];
    }

    public function getCurrencyMap()
    {
        return [
            self::CURRENCY_CNY,
            self::CURRENCY_USD
        ];
    }

    /**
     * 获取开发者模式 map
     * @return array
     */
    public static function getPublisherTypeMap()
    {
        return [
            self::TYPE_WHITE => "White",
            self::TYPE_BLACK => "Black",
        ];
    }

    public function getUnitRepeatSwitchMap()
    {
        return [
            self::UNIT_REPEAT_SWITCH_OFF => "关闭",
            self::UNIT_REPEAT_SWITCH_ON => "打开",
        ];
    }

    public function getMyOfferSwitchMap()
    {
        return [
            self::MY_OFFER_SWITCH_OFF => "关闭",
            self::MY_OFFER_SWITCH_ON => "打开",
        ];
    }

    public function getSubAccountSwitchMap()
    {
        return [
            self::SUB_ACCOUNT_SWITCH_OFF => "关闭",
            self::SUB_ACCOUNT_SWITCH_ON  => "打开",
        ];
    }

    public function getDistributionSwitchMap()
    {
        return [
            self::DIST_SWITCH_OFF => "关闭",
            self::DIST_SWITCH_ON  => "打开",
        ];
    }

    /**
     * 广告平台多账号配置 Map
     * @return array
     */
    public function getNetworkMultipleSwitchMap()
    {
        return [
            self::NETWORK_MULTIPLE_SWITCH_OFF => "关闭",
            self::NETWORK_MULTIPLE_SWITCH_ON  => "打开",
        ];
    }

    /**
     * Report Timezone Map
     * @return array
     */
    public function getReportTimezoneSwitchMap()
    {
        return [
            self::REPORT_TIMEZONE_SWITCH_OFF => "关闭",
            self::REPORT_TIMEZONE_SWITCH_ON  => "打开",
        ];
    }

    /**
     * 广告场景 Map
     * @return array
     */
    public function getScenarioSwitchMap()
    {
        return [
            self::SCENARIO_SWITCH_OFF => "关闭",
            self::SCENARIO_SWITCH_ON  => "打开",
        ];
    }

    /**
     * ADX
     *
     * @return string[]
     */
    public function getAdxSwitchMap()
    {
        return [
            self::ADX_SWITCH_OFF => "关闭",
            self::ADX_SWITCH_ON  => "打开",
        ];
    }

    /**
     * ADX Unit Waterfall关系
     *
     * @return string[]
     */
    public function getAdxUnitSwitchMap()
    {
        return [
            self::ADX_UNIT_SWITCH_OFF => "关闭",
            self::ADX_UNIT_SWITCH_ON  => "打开",
        ];
    }

    /**
     * 获取注册来源名称
     * @param $source
     * @return mixed
     */
    public function getSourceName($source)
    {
        if($source <= 0){
            $source = 1;
        }
        $map = $this->getSourceMap();
        return $map[$source];
    }

    /**
     * 获取所有注册来源映射配置
     * @return array
     */
    public function getSourceMap()
    {
        $map = [
            self::SOURCE_OTHER           => 'Market Activity',
            self::SOURCE_MARKET_ACTIVITY => 'Friends',
            self::SOURCE_FRIENDS         => 'Search Engine',
            self::SOURCE_SEARCH_ENGINE   => 'Other',
        ];

        return $map;
    }

    /**
     * 获取渠道名称
     * @param  int $channelId
     * @return mixed
     */
    public static function getChannelName($channelId)
    {
        $map = self::getChannelMap();
        return $map[$channelId];
    }

    /**
     * 获取所有渠道映射配置
     * @return array
     */
    public static function getChannelMap()
    {
        return [
            self::CHANNEL_TOPON => 'TopOn',
            self::CHANNEL_233   => '233',
            self::CHANNEL_DLADS => 'Dlads',
        ];
    }

    /**
     * 获取所有渠道域名配置
     * @return array
     */
    public function getChannelDomainMap(): array
    {
        return [
            self::CHANNEL_TOPON => self::CHANNEL_DOMAIN_TOPON,
            self::CHANNEL_233   => self::CHANNEL_DOMAIN_233,
            self::CHANNEL_DLADS => self::CHANNEL_DOMAIN_DLADS,
        ];
    }

    /**
     * 根据渠道 ID 获取渠道域名
     * @param mixed $channelId 渠道 ID
     * @return string 渠道域名
     */
    public function getChannelDomain($channelId): string
    {
        $channelDomainMap = $this->getChannelDomainMap();
        return $channelDomainMap[(int)$channelId];
    }

    /**
     * 获取所有Publisher ID
     * @return array
     */
    public static function getAllPublisherId()
    {
        return array_column(self::query()->get(['id'])->toArray(), 'id');
    }

    /**
     * 获取 渠道 的所有Publisher ID
     *
     * @param  int   $channelId
     * @param  array $publisherIds
     * @return array
     */
    public function getAllPublisherIdForChannel($channelId, $publisherIds = []){
        $query = $this->where('channel_id', $channelId);

        if(!empty($publisherIds)){
            $query->whereIn('id', $publisherIds);
        }

        $ids = $query->get(['id'])->toArray();
        if(empty($ids)){
            return [];
        }
        return array_column($ids, 'id');
    }

    /**
     * 更新子账号状态（通过父帐号ID）
     * @param  int $parentPublisherId
     * @param  int $status
     * @return bool
     */
    public function updateStatusByParentId($parentPublisherId, $status){
        if(!in_array($status, array_keys($this->getStatusMap()))){
            return false;
        }
        $this->where('sub_account_parent', $parentPublisherId)
            ->update([
                'status'      => $status,
                'update_time' => time(),
                'admin_id'    => Auth::id()
            ]);

        return true;
    }
}
