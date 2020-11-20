<?php

namespace App\Http\Controllers;

use App\Helpers\Export;
use App\Helpers\Format;
use App\Models\MySql\App;
use App\Models\MySql\Geo;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Unit;
use App\Services\UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportTcApiController extends ApiController
{
    /**
     * BI 返回参数表 data 的字段
     */
    const METRICS_DATE_TIME            = 'date_time';              // 时间
    const METRICS_NW_FIRM_ID           = 'nw_firm_id';             // 广告平台
    const METRICS_UNIT_ID              = 'unit_id';                // AD Source
    const METRICS_SDK_VERSION          = 'sdk_version';            // SDK 版本
    const METRICS_APP_VERSION          = 'app_version';            // APP 版本
    const METRICS_PLATFORM             = 'platform';               // 系统平台 android/ios
    const METRICS_GEO_SHORT            = 'geo_short';              // 地区
    const METRICS_PUBLISH_ID           = 'publisher_id';           // 开发者 ID
    const METRICS_APP_ID               = 'app_id';                 // 应用 ID
    const METRICS_PLACEMENT_ID         = 'placement_id';           // 广告位 ID
    const METRICS_AD_FORMAT            = 'format';                 // 广告位类型
    const METRICS_IS_CN_SDK            = 'is_cn_sdk';              // 是否是国内安卓
    const METRICS_DEVICE_TYPE          = 'device_type';            // 设备类型 手机/平板
    const METRICS_OFFER_PACKAGE        = 'offer_pkg';              // 包名
    const METRICS_OFFER_TITLE          = 'offer_title';            // 应用名称
    const METRICS_OFFER_TITLE_S        = 'offer_title_s';          // 标题
    const METRICS_OFFER_TITLE_DESC     = 'offer_desc';             // 描述
    const METRICS_IS_QCC               = 'is_qcc';                 // 是否是 QCC
    const METRICS_QCC_IMPRESSION       = 'qcc_impression';         // QCC 展示数
    const METRICS_QCC_CLICK            = 'qcc_click';              // QCC 点击数
    const METRICS_CTR                  = 'ctr';                // CTR 点击数/展示数
    const METRICS_TC_REQUEST           = 'tc_request';             // TC 请求数
    const METRICS_TC_FILL_REQUEST      = 'tc_filled_request';      // TC 填充数
    const METRICS_TC_CLICK             = 'tc_click';               // TC 点击数
    const METRICS_TC_INSTALL           = 'tc_install';             // TC 安装数
    const METRICS_TC_REVENUE           = 'tc_revenue';             // TC 收益
    const METRICS_TC_CVR               = 'tc_cvr';                 // TC CVR
    const METRICS_TC_REQUEST_RATE      = 'tc_request_click_rate';  // TC 请求响应率
    const METRICS_TC_FILL_REQUEST_RATE = 'tc_filled_request_rate'; // TC 填充率
    const METRICS_TC_CLICK_RATE        = 'tc_ctr';                 // TC 点击率

    /**
     * BI 返回参数表 data 不包含字段
     */
    const METRICS_APP_NAME             = 'app_name';               // 应用名称
    const METRICS_APP_UUID             = 'app_uuid';               // 应用 UUID
    const METRICS_PLACEMENT_NAME       = 'placement_name';         // 广告位名称
    const METRICS_PLACEMENT_UUID       = 'placement_uuid';         // 应用 UUID
    const METRICS_UNIT_NAME            = 'unit_name';              // Ad Source 名称
    const METRICS_GROUP_BY             = 'group_by';               // Group By
    const METRICS_ORDER_BY             = 'order_by';               // 排序
    const METRICS_EXPORT               = 'export';                 // 导出
    const METRICS_PUB_NAME             = 'publisher_name';         // 开发者昵称
    const METRICS_PUB_LOGIN_URL        = 'pub_login_url';          // 登录开发者链接

//    const DEFAULT_SORT_PARAM      =
//        '[["'
//        . self::METRICS_DATE_TIME
//        . '", "desc"], ["'
//        . self::METRICS_TC_REVENUE
//        . '", "desc"], ["'
//        . self::METRICS_QCC_IMPRESSION
//        . '", "desc"]]';

    const ORDER_BY_PRIMARY   = '[["' . self::METRICS_DATE_TIME . '","desc"]]';
    const ORDER_BY_SECONDARY = '[["' . self::METRICS_TC_REVENUE . '","desc"]]';

    /**
     * @var array 可选维度数组，页面显示可 Group By 的字段
     * 但可 Group By 字段不止于此，格式：字段 => 名字
     */
    
//    protected $selectableDimensions = [
//        self::METRICS_DATE_TIME        => '日期',
//        self::METRICS_PUBLISH_ID       => '开发者',
//        self::METRICS_APP_ID           => '应用',
//        self::METRICS_PLACEMENT_ID     => '广告位',
//        self::METRICS_AD_FORMAT        => '广告样式',
//        self::METRICS_NW_FIRM_ID       => '广告平台',
//        self::METRICS_UNIT_ID          => '广告源',
//        self::METRICS_GEO_SHORT        => '地区',
//        self::METRICS_SDK_VERSION      => 'SDK版本',
//        self::METRICS_APP_VERSION      => 'APP版本',
//        self::METRICS_OFFER_PACKAGE    => '包名',
//        self::METRICS_OFFER_TITLE      => '应用名称',
//        self::METRICS_OFFER_TITLE_S    => '标题',
//        self::METRICS_OFFER_TITLE_DESC => '描述',
//        self::METRICS_PLATFORM         => '系统平台',
//    ];
    
    protected static function getSelectableDimensions()
    {
        return [
            self::METRICS_DATE_TIME        => __('export.date_time'),
            self::METRICS_PUBLISH_ID       => __('export.publisher_id'),
            self::METRICS_APP_ID           => __('export.app_id'),
            self::METRICS_PLACEMENT_ID     => __('export.placement_id'),
            self::METRICS_AD_FORMAT        => __('export.format'),
            self::METRICS_NW_FIRM_ID       => __('export.nw_firm_id'),
            self::METRICS_UNIT_ID          => __('export.unit_id'),
            self::METRICS_GEO_SHORT        => __('export.geo_short'),
            self::METRICS_SDK_VERSION      => __('export.sdk_version'),
            self::METRICS_APP_VERSION      => __('export.app_version'),
            self::METRICS_OFFER_PACKAGE    => __('export.offer_pkg'),
            self::METRICS_OFFER_TITLE      => __('export.offer_title'),
            self::METRICS_OFFER_TITLE_S    => __('export.offer_title_s'),
            self::METRICS_OFFER_TITLE_DESC => __('export.offer_desc'),
            self::METRICS_PLATFORM         => __('export.platform'),
        ];
    }

    /**
     * @var array 固定指标数组
     */
//    protected $fixMetricsMap = [
//        self::METRICS_QCC_IMPRESSION        => '展示',
//        self::METRICS_QCC_CLICK             => '点击',
//        self::METRICS_TC_REQUEST            => 'TC 请求',
//        self::METRICS_TC_REQUEST_RATE       => 'TC 请求接收率',
//        self::METRICS_TC_FILL_REQUEST       => 'TC 填充',
//        self::METRICS_TC_FILL_REQUEST_RATE  => 'TC 填充率',
//        self::METRICS_TC_CLICK              => 'TC 点击',
//        self::METRICS_TC_CLICK_RATE         => 'TC 点击率',
//        self::METRICS_TC_INSTALL            => 'TC 安装',
//        self::METRICS_TC_CVR                => 'TC CVR',
//        self::METRICS_TC_REVENUE            => 'TC 收益',
//    ];
    
    protected static function getFixMetricsMap()
    {
        return [
            self::METRICS_QCC_IMPRESSION        => __('export.qcc_impression'),
            self::METRICS_QCC_CLICK             => __('export.qcc_click'),
            self::METRICS_CTR               => __('export.ctr'),
            self::METRICS_TC_REQUEST            => __('export.tc_request'),
            self::METRICS_TC_REQUEST_RATE       => __('export.tc_request_click_rate'),
            self::METRICS_TC_FILL_REQUEST       => __('export.tc_filled_request'),
            self::METRICS_TC_FILL_REQUEST_RATE  => __('export.tc_filled_request_rate'),
            self::METRICS_TC_CLICK              => __('export.tc_click'),
            self::METRICS_TC_CLICK_RATE         => __('export.tc_ctr'),
            self::METRICS_TC_INSTALL            => __('export.tc_install'),
            self::METRICS_TC_CVR                => __('export.tc_cvr'),
            self::METRICS_TC_REVENUE            => __('export.tc_revenue'),
        ];
    }

    /**
     * @var array 支持 GroupBy 的所有字段，根据 BI 设置后请勿更改！
     */
    protected $allowGroupByMetrics = [
        self::METRICS_DATE_TIME,
        self::METRICS_APP_ID,
        self::METRICS_PLACEMENT_ID,
        self::METRICS_AD_FORMAT,
        self::METRICS_NW_FIRM_ID,
        self::METRICS_UNIT_ID,
        self::METRICS_PLATFORM,
        self::METRICS_SDK_VERSION,
        self::METRICS_APP_VERSION,
        self::METRICS_OFFER_PACKAGE,
        self::METRICS_GEO_SHORT,
        self::METRICS_PUBLISH_ID,
        self::METRICS_DEVICE_TYPE,
        self::METRICS_IS_CN_SDK,
        self::METRICS_OFFER_TITLE
    ];

    /**
     * @var array 允许 order By 的字段列表，根据 BI 设置后请勿更改！
     */
    protected $sortableMetrics = [
        self::METRICS_DATE_TIME,
        self::METRICS_QCC_IMPRESSION,
        self::METRICS_QCC_CLICK,
        self::METRICS_TC_REQUEST,
        self::METRICS_TC_FILL_REQUEST,
        self::METRICS_TC_CLICK,
        self::METRICS_TC_INSTALL,
//        self::METRICS_TC_CVR,   不支持
        self::METRICS_TC_REVENUE,
    ];

    /**
     * @var array 请求体参数数组
     * 参数名 => 默认值
     * 注：前端传来的 order by 是 string 类型，为保持一致此处 order by 默认值为 string
     */
    protected $requestParams = [
        self::METRICS_GROUP_BY         => [self::METRICS_DATE_TIME],
        self::METRICS_ORDER_BY         => self::ORDER_BY_PRIMARY,
        self::METRICS_DATE_TIME        => '',
        self::METRICS_PUBLISH_ID       => '',
        self::METRICS_APP_ID           => '',
        self::METRICS_PLACEMENT_ID     => '',
        self::METRICS_AD_FORMAT        => '',
        self::METRICS_NW_FIRM_ID       => '',
        self::METRICS_UNIT_ID          => '',
        self::METRICS_GEO_SHORT        => [],
        self::METRICS_PLATFORM         => 0,
        self::METRICS_SDK_VERSION      => '',
        self::METRICS_APP_VERSION      => '',
        self::METRICS_OFFER_PACKAGE    => '',
        self::METRICS_OFFER_TITLE      => '',
        self::METRICS_OFFER_TITLE_S    => '',
        self::METRICS_OFFER_TITLE_DESC => '',
    ];

    /**
     * @var array BI 接口可选的请求参数表<br/>
     * <a href="https://confluence.magicgame001.com/pages/viewpage.action?pageId=18754056">tc/report 数据接口</a><br/>
     * 接口请求字段 : 接口返回字段
     */
    protected $optionalBIParams = [
        'app_id_list'           => self::METRICS_APP_ID,
        'placement_id_list'     => self::METRICS_PLACEMENT_ID,
        'geo_short_list'        => self::METRICS_GEO_SHORT,
        'format_list'           => self::METRICS_AD_FORMAT,
        'nw_firm_id_list'       => self::METRICS_NW_FIRM_ID,
        'unit_id_list'          => self::METRICS_UNIT_ID,
        'platform_list'         => self::METRICS_PLATFORM,
        'sdk_version_list'      => self::METRICS_SDK_VERSION,
        'app_version_list'      => self::METRICS_APP_VERSION,
        'offer_pkg_list'        => self::METRICS_OFFER_PACKAGE,
        'publisher_id_list'     => self::METRICS_PUBLISH_ID,
        'device_type_list'      => null,
        'is_cn_sdk_list'        => null,
        'offer_title_list'      => self::METRICS_OFFER_TITLE,
        'offer_title_s_list'    => self::METRICS_OFFER_TITLE_S,
        'offer_desc_list'       => self::METRICS_OFFER_TITLE_DESC,
    ];

    /**
     * @var array 筛选框数组
     * 注：不建议使用关联数组
     */
    protected $selectionList = [
        [
            'name' => self::METRICS_AD_FORMAT,
            'placeholder' => '',
            'options' => [],
            'value' => 0,
        ],
        [
            'name' => self::METRICS_NW_FIRM_ID,
            'placeholder' => '',
            'options' => [],
            'value' => 0,
        ],
        [
            'name' => self::METRICS_PLATFORM,
            'placeholder' => '',
            'options' => [],
            'value' => 0,
        ],
    ];

//    /**
//     * @var array 输入框数组
//     * 注：不建议使用关联数组
//     */
//    protected $inputFieldList = [
//        [
//            'name'        => self::METRICS_DATE_TIME,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_PUBLISH_ID,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'number'
//        ],
//        [
//            'name'        => self::METRICS_APP_ID,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_PLACEMENT_ID,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_UNIT_ID,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'number'
//        ],
//        [
//            'name'        => self::METRICS_SDK_VERSION,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_APP_VERSION,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_OFFER_PACKAGE,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_OFFER_TITLE,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_OFFER_TITLE_S,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//        [
//            'name'        => self::METRICS_OFFER_TITLE_DESC,
//            'placeholder' => '',
//            'value'       => '',
//            'type'        => 'text'
//        ],
//    ];

    /**
     * @var array 需要货币显示的字段
     */
    protected $currencyMetrics = [
        self::METRICS_TC_REVENUE
    ];

    /**
     * @var array 百分比显示的字段
     */
    protected $percentageMetrics = [
        self::METRICS_TC_CVR,
        self::METRICS_TC_REQUEST_RATE,
        self::METRICS_TC_FILL_REQUEST_RATE,
        self::METRICS_TC_CLICK_RATE,
        self::METRICS_CTR
    ];

    /**
     * @var array 标红的字段
     */
    protected $strikingMetrics = [
        self::METRICS_TC_CVR,
        self::METRICS_TC_REVENUE
    ];

    public function index(Request $request)
    {
        //历史遗留问题：api没启用ConvertEmptyStringsToNull中间件 需将空字符串转换为null 否则请求bi会错误
        foreach($request->all() as $key=>$val){
            $request->offsetSet($key,is_string($val) && $val === '' ? null : $val);
        }
        //导出暂时方案
        if(stripos($request->url(),'/export')!==false){
            $request->offsetSet('export',1);
        }

        //$this->checkAccessPermission('report-tc@index');
        $pageNo   = $request->input('page_no',1);
        $pageSize = $request->input('page_size',20);
        //dd($request->all());
        /* 获取所有表单输入 */
        $input = [];
        foreach ($this->requestParams as $param => $defaultValue) {
            $tmpData = $request->input($param);
            /* 若表单无输入，则使用默认数据，get 自带的 default 只有没有该参数时才生效 */
            $input[$param] = $tmpData ?? $defaultValue;
            switch ($param) {
                /* 这三个使用默认值或者接收值即可，单独处理 */
                case self::METRICS_ORDER_BY:
                case self::METRICS_GROUP_BY:
                case self::METRICS_DATE_TIME:
                    break;
                /* Geo short 传入的本就是数组，不需进行操作。 */
                case self::METRICS_GEO_SHORT:
                    continue 2;
                /* 将 uuid 转化为 id */
                case self::METRICS_APP_ID:
                case self::METRICS_PLACEMENT_ID:
                    if ($input[$param] === $defaultValue) {
                        break;
                    }
                    $input[$param . '_uuid'] = $input[$param];
                    $model = $param === self::METRICS_APP_ID ? (new App()) : (new Placement());
                    $idList = $model->newQuery()->where('id',$input[$param])->orWhere('uuid',$input[$param])->pluck('id')->toArray();
                    if (empty($idList)) {
                        throw new HttpResponseException($this->jsonResponse([],10000, '未找到相应'.self::getSelectableDimensions()[$param] . '记录:'.$input[$param]));
                    }
                    $input[$param] = $idList;
                    break;
                /* 将传入值改成 int 数组类型 */
                case self::METRICS_PUBLISH_ID:
                case self::METRICS_AD_FORMAT:
                case self::METRICS_NW_FIRM_ID:
                case self::METRICS_PLATFORM:
                case self::METRICS_UNIT_ID:
                    $input[$param] = $input[$param] === $defaultValue ? $defaultValue : [(int)$input[$param]];
                    break;
                /* 默认将传入值转成数组类型 */
                default:
                    $input[$param] = $input[$param] === $defaultValue ? $defaultValue : [$input[$param]];
            }
        }
        
//        return $input;

        /* 处理 Group By */
        $selectedDimensions = $input[self::METRICS_GROUP_BY];
        $groupByMetricsMap = self::getSelectableDimensions(); // 复制所有可 group by 的维度
        $unselectedDimensions = array_values(array_diff(array_keys($groupByMetricsMap), $selectedDimensions));
        foreach ($unselectedDimensions as $dimension) {
            unset($groupByMetricsMap[$dimension]);
        }
        /* 初始化地区 Map */
        $geoMap = Geo::getGeoMap();

        /* 处理 Order By */
        $orderByStr = $input[self::METRICS_ORDER_BY];
        if ($orderByStr === self::ORDER_BY_PRIMARY) {
            if (!in_array(self::METRICS_DATE_TIME, $selectedDimensions, false) || count($selectedDimensions) > 1) {
                $orderByStr = self::ORDER_BY_SECONDARY;
            }
        }
        $orderByList = json_decode($orderByStr, true);
//        return $orderByList;
        /* 没有 Group By DATE_TIME 处理 */
//        if (!in_array(self::METRICS_DATE_TIME, $selectedDimensions, false)) {
//            if (count($orderByList) > 1) {
//                for ($i = 0, $iMax = count($orderByList); $i < $iMax; $i++) {
//                    if ($orderByList[$i][0] === self::METRICS_DATE_TIME) {
//                        array_splice($orderByList, $i, 1);
//                        --$iMax;
//                    }
//                }
//            } else if ($orderByList[0][0] === self::METRICS_DATE_TIME) {
//                $orderByList[0][0] = self::METRICS_TC_REVENUE;
//                $orderByList[0][1] = 'desc';
//                $orderByList[] = [self::METRICS_QCC_IMPRESSION, 'desc'];
//            }
//        }
//        return $orderByStr;
        $orderByMap = [];
        foreach ((array)$orderByList as $item) {
            $orderByMap[$item[0]] = $item[1];
        }

        /* 处理时间范围 */
        $begTimeSeconds = time() - 24 * 60 * 60 * 6;
        $endTimeSeconds = time();
        if ($input[self::METRICS_DATE_TIME] !== '') {
            list($begTimeStr, $endTimeStr) = explode('-', $input[self::METRICS_DATE_TIME]);
            $begTimeSeconds = strtotime(trim($begTimeStr));
            $endTimeSeconds = strtotime(trim($endTimeStr));
        }
        $daterange = date('m/d/Y', $begTimeSeconds) . ' - ' . date('m/d/Y', $endTimeSeconds);
        $input[self::METRICS_DATE_TIME] = $daterange;

        /* 初始化所有输入框 */
//        for ($i = 0, $iMax = count($this->inputFieldList); $i < $iMax; $i++) {
//            $metric = $this->inputFieldList[$i]['name'];
//            $this->inputFieldList[$i]['placeholder'] = self::getSelectableDimensions()[$metric];
//            if ($metric === self::METRICS_DATE_TIME) {
//                $this->inputFieldList[$i]['value'] = $input[$metric];
//            } else if ($metric === self::METRICS_APP_ID || $metric === self::METRICS_PLACEMENT_ID) {
//                $this->inputFieldList[$i]['value'] = $input[$metric . '_uuid'] ?? '';
//            } else {
//                $this->inputFieldList[$i]['value'] = count($input[$metric]) === 0 ? '' : $input[$metric][0];
//            }
//        }

        /* 初始化所有下拉框 */
        $selectionOptions = [
            self::METRICS_AD_FORMAT  => NetworkFirm::getFormatMap(),
            self::METRICS_NW_FIRM_ID => NetworkFirm::getNwFirmMap(),
            self::METRICS_PLATFORM   => NetworkFirm::getPlatformMap()
        ];
//        for ($i = 0, $iMax = count($this->selectionList); $i < $iMax; $i++) {
//            $metric = $this->selectionList[$i]['name'];
//            $this->selectionList[$i]['value']       = count($input[$metric]) === 0 ? '' : $input[$metric][0];
//            $this->selectionList[$i]['placeholder'] = self::getSelectableDimensions()[$metric];
//            $this->selectionList[$i]['options']     = $selectionOptions[$metric];
//        }

        /* 处理分页请求参数 */
//        $limit = $pageSize;
//        $page  = $pageNo;
        $limit = $request->input('export') == 1 ? 1000 : $pageSize;

        // 接口请求的参数表，默认添加所有必要的请求参数
        $param = [
            'start_date'        => (int)date('Ymd', $begTimeSeconds),
            'end_date'          => (int)date('Ymd', $endTimeSeconds),
            'group_by'          => array_keys($groupByMetricsMap),
            'order_by'          => $orderByList,
            'offset'            => (int)($pageNo - 1) * $pageSize,
            'limit'             => (int)$limit,
        ];

        /* 追加非必要的请求参数，若非该参数有值，则加入 */
        foreach ($this->optionalBIParams as $optionalParam => $paramMetric) {
            if ($paramMetric === null) {                                        // 该参数用户无法输入，不追加
                continue;
            }
            if ($input[$paramMetric] === $this->requestParams[$paramMetric]) {  // 该参数 request 等于缺省值，表示无传入值，不追加
                continue;
            }
            $param[$optionalParam] = $input[$paramMetric];
        }

        /* 过滤开发者 */
        if (in_array('publisher_id', $param['group_by'], true)
            || in_array('app_id', $param['group_by'], true)
            || in_array('placement_id', $param['group_by'], true)
            || $request->has('publisher_id')
            || $request->has('app_id')
            || $request->has('placement_id')) {
            /* 用户可视的开发者列表 */
            $publicPublisherIds = UserService::getPublisherIdListByUserId(auth()->id());

            /* 如果开发者列表无搜索，使用允许列表中的开发者，如果有搜索，取并集 */
            if (array_key_exists('publisher_id_list', $param)) {
                $param['publisher_id_list'] = array_values(array_intersect($publicPublisherIds, $param['publisher_id_list']));
            } else {
                $param['publisher_id_list'] = $publicPublisherIds;
            }
            if (empty($param['publisher_id_list'])) {
                $param['publisher_id_list'] = [0];
            }
        }

//        return $param;

        $data  = $this->getBiData($param);
        $count = $data['count'];
        $data  = $data['list'];
        
        //计算ctr
        foreach($data as $key=>$val){
            $data[$key]['ctr'] = round($val['qcc_click']/$val['qcc_impression'],4);
        }

        /* 处理导出表格、显示列表处理方式相同的数据 */
        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            /* 处理地区 */
            $data[$i][self::METRICS_GEO_SHORT] =
                array_key_exists($data[$i][self::METRICS_GEO_SHORT], $geoMap)
                    ? $geoMap[$data[$i][self::METRICS_GEO_SHORT]]
                    : '-';
            /* 处理 Network、format、Platform */
            foreach ($selectionOptions as $metric => $options) {
                $data[$i][$metric] =
                    array_key_exists($data[$i][$metric], $options)
                        ? $options[$data[$i][$metric]]
                        : '-';
            }
        }

        /* 导出表格 */
        if ($request->input('export') == 1) {
            /* 表头增加已选维度关联数据，unset 用于调整列顺序 */
            for ($i = 0, $iMax = count($selectedDimensions); $i < $iMax; $i++) {
                unset($groupByMetricsMap[$selectedDimensions[$i]]);
                switch ($selectedDimensions[$i]) {
                    case self::METRICS_APP_ID:
                        $groupByMetricsMap[self::METRICS_APP_ID]         = __('export.app_id'); //'应用 ID'
                        $groupByMetricsMap[self::METRICS_APP_UUID]       = __('export.app_uuid'); //'应用 UUID'
                        $groupByMetricsMap[self::METRICS_APP_NAME]       = __('export.app_name'); //'应用名'
                        break;
                    case self::METRICS_PLACEMENT_ID:
                        $groupByMetricsMap[self::METRICS_PLACEMENT_ID]   = __('export.placement_id'); //'广告位 ID'
                        $groupByMetricsMap[self::METRICS_PLACEMENT_UUID] = __('export.placement_uuid'); //'广告位 UUID'
                        $groupByMetricsMap[self::METRICS_PLACEMENT_NAME] = __('export.placement_name'); //'广告位名称'
                        break;
                    case self::METRICS_UNIT_ID:
                        $groupByMetricsMap[self::METRICS_UNIT_ID]        = __('export.unit_id'); //'广告源 ID'
                        $groupByMetricsMap[self::METRICS_UNIT_NAME]      = __('export.unit_name'); //'广告源名称'
                        break;
                    case self::METRICS_PUBLISH_ID:
                        $groupByMetricsMap[self::METRICS_PUBLISH_ID]     = __('export.publisher_id'); //'开发者 ID'
                        $groupByMetricsMap[self::METRICS_PUB_NAME]       = __('export.publisher_name'); //'开发者名称'
                        break;
                    default:
                        $groupByMetricsMap[$selectedDimensions[$i]] = self::getSelectableDimensions()[$selectedDimensions[$i]];
                }
            }
            /* 处理表格数据 */
            for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
                /* 追加表头缺失的数据或者处理表头要求字段的显示格式 注：data 中已有 METRICS_OFFER_TITLE 字段  */
                foreach ($selectedDimensions as $dimension) {
                    switch ($dimension) {
                        /* 追加 APP_NAME 和处理 APP_ID 显示 */
                        case self::METRICS_APP_ID:
                            $app = App::query()->find($data[$i][$dimension]);
                            $data[$i][self::METRICS_APP_UUID] = isset($app) ? $app->uuid : '-';
                            $data[$i][self::METRICS_APP_NAME] = isset($app) ? $app->name : '-';
                            break;
                        /* 追加 PLACEMENT_NAME 和处理 PLACEMENT_ID 显示 */
                        case self::METRICS_PLACEMENT_ID:
                            $placement = Placement::query()->find($data[$i][$dimension]);
                            $data[$i][self::METRICS_PLACEMENT_UUID] = isset($placement) ? $placement->uuid : '-';
                            $data[$i][self::METRICS_PLACEMENT_NAME] = isset($placement) ? $placement->name : '-';
                            break;
                        /* 追加 UNIT_NAME */
                        case self::METRICS_UNIT_ID:
                            $unit = Unit::query()->find($data[$i][$dimension]);
                            $data[$i][self::METRICS_UNIT_NAME] = isset($unit) ? $unit->name : '-';
                            break;
                        /* 追加开发者名称 */
                        case self::METRICS_PUBLISH_ID:
                            $publisher = Publisher::query()->find($data[$i][$dimension]);
                            $data[$i][self::METRICS_PUB_NAME] = isset($publisher) ? $publisher->name : '-';
                            break;
                    }
                }
                /* 清除表头不需要的数据 */
                foreach (self::getSelectableDimensions() as $metric => $name) {
                    if (!in_array($metric, $selectedDimensions, false)) {
                        unset($data[$i][$metric]);
                    }
                }
            }
//            return $data;
            $headers = array_merge($groupByMetricsMap, self::getFixMetricsMap());
            Export::excel($headers, $data, true, 'report-tc' . date('Y-m-d H:i:s') . '-export');
            exit;
        }
        
        /* 处理已选维度的数据 */
        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            foreach (self::getSelectableDimensions() as $metric => $name) {
                /* 这四个维度的数据，没有的话显示 - */
                if (in_array($metric, [self::METRICS_OFFER_PACKAGE, self::METRICS_OFFER_TITLE, self::METRICS_OFFER_TITLE_S, self::METRICS_OFFER_TITLE_DESC], true)) {
                    $data[$i][$metric] = empty($data[$i][$metric]) ? '-' : $data[$i][$metric];
                }

                /* 可选维度列表中未被选中的维度，删除 */
                if (!in_array($metric, $selectedDimensions, false)) {
                    if ($metric !== self::METRICS_OFFER_TITLE) {
                        unset($data[$i][$metric]);
                    }
                    continue;
                }
                $tmpData = null;
                $idMetric = 'id';
                switch ($metric) {
                    /* publisher id 获取 publisher name 和生成登录链接 */
                    case self::METRICS_PUBLISH_ID:
                        $tmpData = Publisher::query()->where('id', $data[$i][$metric])->first();
                        //$data[$i][self::METRICS_PUB_LOGIN_URL] = '/publisher/login?id=' . $data[$i][$metric];
                        $data[$i]['publisher_u_id'] = $tmpData['id'];
                        $data[$i]['publisher_u_email'] = $tmpData['email'];
                        break;
                    /* app id 替换为 uuid */
                    case self::METRICS_APP_ID:
                        $tmpData = App::query()->where('id', $data[$i][$metric])->first();
                        $idMetric = 'uuid';
                        break;
                    /* placement id 替换未 uuid */
                    case self::METRICS_PLACEMENT_ID:
                        $tmpData = Placement::query()->where('id', $data[$i][$metric])->first();
                        $idMetric = 'uuid';
                        break;
                    /* unit id 获取 unit name */
                    case self::METRICS_UNIT_ID:
                        $tmpData = Unit::query()->where('id', $data[$i][$metric])->first();
                        break;
                    default:
                        continue 2;
                }
                if ($tmpData == null) {
                    $data[$i][$metric] = '-<br/>-';
                } else {
                    $data[$i][$metric] = $tmpData['name'] . '<br/>' . $tmpData[$idMetric];
                }
            }
        }
        
        //格式化比率显示
        foreach($data as &$val){
            foreach($val as $k=>$v){
                if(in_array($k,$this->percentageMetrics)){
                    $val[$k] = Format::rateToPer($v);
                }
            }
        }
        
        //头部信息
        $headers = array_keys(array_merge($groupByMetricsMap, self::getFixMetricsMap()));
//
//        /* 处理分页显示 */
//        $report = Pagination::paginate($count, $data, $limit, 'page', $page);
////        return $report;
//
//        return view('report-tc.index')
//            ->with('selectableDimensions', $this->selectableDimensions)
//            ->with('selectedDimensions', $selectedDimensions)
//            ->with('selectionList', $this->selecttaionList)
//            ->with('inputFieldList', $this->inputFieldList)
//            ->with('groupByMetricsMap', $groupByMetricsMap)
//            ->with('sortableMetrics', $this->sortableMetrics)
//            ->with('fixMetricsMap', $this->fixMetricsMap)
//            ->with('currencyMetrics', $this->currencyMetrics)
//            ->with('percentageMetrics', $this->percentageMetrics)
//            ->with('strikingMetrics', $this->strikingMetrics)
//            ->with('adFormatMap', NetworkFirm::getFormatMap())
//            ->with('nwFirmMap', NetworkFirm::getNwFirmMap())
//            ->with('geoMap', $geoMap)
//            ->with('geoSelected', $input[self::METRICS_GEO_SHORT])
//            ->with('orderByMap', $orderByMap)
//            ->with('daterange', $daterange)
//            ->with('pageAppends', $request->all())
//            ->with('data', $report)
//            ;
    
        $list = array_slice($data,($pageNo-1)*$pageSize, $pageSize);
        $resData = [
            'total'                   => (int)$count,
            'page_no'                 => (int)$pageNo,
            'page_size'               => (int)$pageSize,
            'groupByMetrics'          => array_keys($groupByMetricsMap),
            'strikingMetrics'         => $this->strikingMetrics,
            'header'                  => $headers,
            'list'                    => $data,
            
        ];
    
        return $this->jsonResponse($resData);
    }

    private function getBiData($param): array
    {
        $biApi = env('BI_SERVICE_ADMIN_TC_REPORT');
        Log::info('ReportTcController getBiData: ' . ' API: ' . $biApi . '; param: ', $param);

        $data = [
            'count' => 0,
            'list' => []
        ];
        $client = new Client();
        try {
            $response = $client->post($biApi, [RequestOptions::JSON => $param]);
            $tmpData = json_decode($response->getBody(), true);
            if ($tmpData !== null) {
                $data['count'] = $tmpData['count'];
                $data['list']  = $tmpData['data'];
            }
        } catch (RequestException $e) {
            Log::info('ReportFullController getBiData error: ' . json_encode($e->getMessage()));
        }
        return $data;
    }
    
    /**
     * 可选数据维度列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function dimensions()
    {
        $data = [];
        foreach(self::getSelectableDimensions() as $key=>$val){
            $data[] = [
                'value' => $key,
                'label' => $val
            ];
        }
        
        return $this->jsonResponse($data);
    }
}