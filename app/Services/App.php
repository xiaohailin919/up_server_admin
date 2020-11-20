<?php

namespace App\Services;

use App\Models\MySql\App as AppMyModel;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\Mongo\App as AppMoModel;
use App\Models\MySql\PublisherGroupRelationship;
use App\Models\MySql\StrategyApp as StrategyAppMyModel;
use App\Models\MySql\StrategyAppFirm as StrategyAppFirmModel;
use App\Models\MySql\StrategyAppLogger;

class App
{

    public static function sync($id)
    {
        $app = self::buildSyncData($id);
        $sync = new Sync('app');
        return $sync->handle($id, $app);
    }

    public static function queueSync($queueId, $id)
    {
        $app = self::buildSyncData($id);
        $sync = new QueueSync('app');
        return $sync->handle($queueId, $id, $app);
    }

    private static function buildSyncData($id)
    {
        $field = [
            'id as app_id',
            'uuid as app_uuid',
            'publisher_id',
            'name',
            'status',
            'private_status',
            'screen_orientation',
            'store_url',
            'app_store_id',
            'tracking_io_app_key',
        ];

        $appMyModel = new AppMyModel();
        $publisherMyModel = new PublisherMyModel();
        $strategyAppMyModel = new StrategyAppMyModel();

        $app = $appMyModel->getOne($field, ['id' => $id]);

        if (!$app) {
            return [];
        }
        $onePublisher = $publisherMyModel->getOne(["api_key", 'system'],['id' => $app['publisher_id']]);
        $app['api_key'] = !empty($onePublisher['api_key']) ? $onePublisher['api_key'] : '';
        $app["system"]  = !empty($onePublisher['system']) ? $onePublisher['system'] : '';

        $strategy = $strategyAppMyModel->getStrategy($id);
        $app['cache_time']            = $strategy['cache_time'];
        $app['n_psid_tm']             = intval($strategy['new_psid_time']) * 1000;
        $app['cache_areasize']        = intval($strategy['cache_areasize']) * 1024;
        $app['placement_timeout']     = $strategy['placement_timeout'];
        $app['collect_material']      = $strategy['collect_material'];
        $app['collect_app_list']      = $strategy['collect_app_list'];
        $app['rf_install']            = $strategy['rf_install'];
        $app['rf_download']           = $strategy['rf_download'];
        $app['rf_key']                = $strategy['rf_key'];
        $app['rf_app_id']             = $strategy['rf_app_id'];
        $app['rf_power']              = $strategy['rf_power'];
        $app['rf_power2']             = $strategy['rf_power2'];
        $app['gdpr_consent_set']      = $strategy['gdpr_consent_set'];
        $app['gdpr_server_only']      = $strategy['gdpr_server_only'];
        $app['gdpr_notify_url']       = $strategy['gdpr_notify_url'];
        $app['notice_list']           = $strategy['notice_list'];
        $app['network_pre_init_list'] = $strategy['network_pre_init_list'];
        $app['network_gdpr_switch']   = intval($strategy['network_gdpr_switch']);
        $app['update_time']           = time();

        // 新结构加 strategy
        $app['strategy']['data_level']       = (object)json_decode($strategy['data_level']);
        $app['strategy']['psid_hot_leave']   = $strategy['psid_hot_leave'] * 1000;
        $app['strategy']['leave_app_switch'] = $strategy['leave_app_switch'] != 2 ? 1 : 2;
        $app['strategy']['crash_sw']         = in_array($strategy['crash_sw'], [1,2], false) ? $strategy['crash_sw'] - 1 : 1;
        $app['strategy']['crash_list']       = json_decode($strategy['crash_list'], true);
        // ADX
        $app['strategy']['req_sw']       = (int)$strategy['req_sw'];
        $app['strategy']['req_addr']     = (string)$strategy['req_addr'];
        $app['strategy']['req_tcp_addr'] = (string)$strategy['req_tcp_addr'];
        $app['strategy']['req_tcp_port'] = (string)$strategy['req_tcp_port'];
        $app['strategy']['bid_sw']       = (int)$strategy['bid_sw'];
        $app['strategy']['bid_addr']     = (string)$strategy['bid_addr'];
        $app['strategy']['tk_sw']        = (int)$strategy['tk_sw'];
        $app['strategy']['tk_addr']      = (string)$strategy['tk_addr'];
        $app['strategy']['tk_tcp_addr']  = (string)$strategy['tk_tcp_addr'];
        $app['strategy']['tk_tcp_port']  = (string)$strategy['tk_tcp_port'];
        $app['strategy']['adx_apk_sw']   = (int)$strategy['adx_apk_sw'];
        $app['strategy']['ol_sw']        = (int)$strategy['ol_sw'];
        $app['strategy']['ol_req_addr']  = (string)$strategy['ol_req_addr'];
        $app['strategy']['ol_tcp_addr']  = (string)$strategy['ol_tcp_addr'];
        $app['strategy']['ol_tcp_port']  = (string)$strategy['ol_tcp_port'];

        /* 增加 PublisherGroupId */
        if (StrategyAppLogger::query()
            ->where('app_id', '=', $id)
            ->where('status', '=', StrategyAppLogger::STATUS_ACTIVE)
            ->exists()) {
            $app['publisher_group_id'] = 0;
        } else {
            $publisherGroupRelationships = PublisherGroupRelationship::query()->where('publisher_id', '=', $app['publisher_id'])->select(['publisher_group_id'])->get()->toArray();
            $publisherGroupIds = array_column($publisherGroupRelationships, 'publisher_group_id');
            $strategyAppLogger = StrategyAppLogger::query()
                ->where('rule_type', '=', StrategyAppLogger::RULE_TYPE_PUBLISHER_GROUP)
                ->where('status', '=', StrategyAppLogger::STATUS_ACTIVE)
                ->whereIn('publisher_group_id', $publisherGroupIds)
                ->orderByDesc('create_time')
                ->first();
            $app['publisher_group_id'] = $strategyAppLogger === null ? 0 : $strategyAppLogger['publisher_group_id'];
        }

        $appFirmModel = new StrategyAppFirmModel();
        $appFirmLists = $appFirmModel->get(["nw_firm_id", 'upload_sw', 'click_only'],['app_id' => $id, 'status' => 2]);

        foreach ($appFirmLists as $k => $v) {
            $app['app_firm_strategy'][$v['nw_firm_id']]['up_sw'] = $v['upload_sw'];
            $app['app_firm_strategy'][$v['nw_firm_id']]['click_sw'] = $v['click_only'];
        }

        if ($app['status'] == AppMyModel::STATUS_RUNNING
            && $app['private_status'] == AppMyModel::PRIVATE_STATUS_RUNNING) {

            $app['status'] = AppMoModel::STATUS_RUNNING;

        } else {
            $app['status'] = AppMoModel::STATUS_STOP;
        }
        unset($app['private_status']);

        return $app;
    }

    /**
     * 构造 App 导出列表表格头部
     *
     * @return array
     */
    public static function generateExportHeader() {
        return [
            'id'             => __('common.app.id'),
            'uuid'           => __('common.app.uuid'),
            'name'           => __('common.app.name'),
            'publisher_id'   => __('common.publisher.id'),
            'publisher_name' => __('common.publisher.name'),
            'platform'       => __('common.app.platform'),
            'package'        => __('common.app.package'),
            'revenue'        => __('common.revenue'),
            'dau'            => __('common.dau'),
            'type'           => __('common.app.type'),
            'label'          => __('common.app.label'),
            'category'       => __('common.app.category'),
            'category_2'     => __('common.app.category_2'),
            'create_time'    => __('common.create_time'),
            'status'         => __('common.app.status'),
        ];
    }

    /**
     * 构造 App 导出列表数据
     *
     * @param $data
     * @return mixed
     */
    public static function generateExportData($data) {
        foreach ($data as &$datum) {
            $datum['platform'] = AppMyModel::getPlatformName($datum['platform']);
            $datum['package'] = $datum['platform'] == AppMyModel::PLATFORM_ANDROID ? $datum['package'] : $datum['bundle_id'];
            $labelsStr = '';
            foreach ($datum['label_list'] as $labelItem) {
                $labelsStr .= $labelItem['label'] . ' ';
            }
            $datum['type'] = $datum['type_name'];
            $datum['label'] = $labelsStr;
            $datum['create_time'] = date('Y-m-d H:i:s', $datum['create_time'] / 1000);
            $datum['status'] = AppMyModel::getStatusName($datum['status']);
        }
        return $data;
    }
}