<?php

namespace App\Console\Commands;

use App\Models\MySql\Base;
use App\Models\MySql\SdkVersion;
use DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ParseSdkVersion extends Command
{
    private static $formatNameIdMap = [
        '原生广告' => Base::FORMAT_NATIVE,
        '激励视频' => Base::FORMAT_RV,
        '横幅广告' => Base::FORMAT_BANNER,
        '插屏广告' => Base::FORMAT_INTERSTITIAL,
        '开屏广告' => Base::FORMAT_SPLASH
    ];

    private static $areaMap = [
        'cn' => SdkVersion::AREA_NATIVE,
        'other' => SdkVersion::AREA_FOREIGN
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '临时用于拉取 docs 中配置的 SDK 版本配置，手动填写效率太低';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /* 逐个接口访问，逐个接口读取后写入 */
        $client = new Client();

        try {
            DB::beginTransaction();

            /* 中文版安卓、iOS */
            $res = $client->get('https://app.toponad.com/a/package/config?lang=zh-cn&platform=android');
            $andIOSZh = json_decode($res->getBody(), true)['data'];

            /* 遍历创建安卓所有版本 */
            $this->createRecord($andIOSZh['android'], SdkVersion::TYPE_AND);

            /* 遍历创建 iOS 所有版本 */
            $this->createRecord($andIOSZh['ios'], SdkVersion::TYPE_IOS);

            /* 英文版安卓、iOS */
            $res = $client->get('https://app.toponad.com/a/package/config?lang=en-us&platform=android');
            $andIOSEn = json_decode($res->getBody(), true)['data'];

            /* 更新英文相关字段 */
            $this->updateEnFields($andIOSEn['android'], SdkVersion::TYPE_AND);
            $this->updateEnFields($andIOSEn['ios'], SdkVersion::TYPE_IOS);

            /* 中文版 Unity */
            $res = $client->get('https://app.toponad.com/a/package/config?lang=zh-cn&platform=unity');
            $unityZh = json_decode($res->getBody(), true)['data'];

            /* 遍历创建 Unity 安卓所有版本 */
            $this->createRecord($unityZh['android'], SdkVersion::TYPE_UNITY_AND);

            /* 遍历创建 Unity iOS 所有版本 */
            $this->createRecord($unityZh['ios'], SdkVersion::TYPE_UNITY_IOS);

            /* 遍历创建 Unity 安卓 + iOS 所有版本 */
            foreach ($unityZh['android+ios'] as $version) {
                SdkVersion::query()->updateOrCreate([
                    'type'    => SdkVersion::TYPE_UNITY_AND_IOS,
                    'version' => $version['version']
                ], [
                    'update_date' => $version['updated'],
                    'size_zh'     => $version['size'],
                    'size_en'     => '',
                    'log_url_zh'  => $version['changeLogURL'],
                    'log_url_en'  => '',
                    'demo_url_zh' => $version['demoURL'],
                    'demo_url_en' => '',
                    'extra'       => json_encode(['unity_ios_version' => $version['version'], 'unity_android_version' => $version['version']]),
                    'status'      => SdkVersion::STATUS_ACTIVE,
                    'admin_id'    => 0
                ]);
            }

            /* 英文版 Unity */
            $res = $client->get('https://app.toponad.com/a/package/config?lang=en-us&platform=unity');
            $unityEn = json_decode($res->getBody(), true)['data'];
            /* 更新英文相关字段 */
            $this->updateEnFields($unityEn['android'], SdkVersion::TYPE_UNITY_AND);
            $this->updateEnFields($unityEn['ios'], SdkVersion::TYPE_UNITY_IOS);
            $this->updateEnFields($unityEn['android+ios'], SdkVersion::TYPE_UNITY_AND_IOS);

            DB::commit();
            $this->info("Exec Success");
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Exec error");
            $this->error("MESSAGE: " . $e->getMessage());
            $this->error("TRACE: " . $e->getTraceAsString());
            $this->error("CODE: " . $e->getCode());
        }
    }

    private function createRecord(array $versionList, $type)
    {
        foreach ($versionList as $version) {
            /* 创建记录 */
            $record = SdkVersion::query()->updateOrCreate([
                'type' => $type,
                'version' => $version['version']
            ], [
                'update_date' => $version['updated'],
                'size_zh'     => $version['size'],
                'size_en'     => '',
                'log_url_zh'  => $version['changeLogURL'],
                'log_url_en'  => '',
                'demo_url_zh' => $version['demoURL'],
                'demo_url_en' => '',
                'status'      => SdkVersion::STATUS_ACTIVE,
                'admin_id'    => 0
            ]);

            /* 创建子类 Network 记录 */
            foreach ($version['networks'] as $network) {

                $network['formats'] = str_replace(["， ", "，", ", "], ',', $network['formats']);
                $formatList = explode(",", $network['formats']);
                $formatIdList = [];
                foreach ($formatList as $format) {
                    $formatIdList[] = self::$formatNameIdMap[$format];
                }

                SdkVersion::query()->updateOrCreate([
                    'parent_id'  => $record['id'],
                    'nw_firm_id' => $network['networkID'],
                    'version'    => $network['version']
                ], [
                    'type'     => SdkVersion::TYPE_NW_FIRM,
                    'area'     => self::$areaMap[$network['country']],
                    'formats'  => json_encode($formatIdList),
                    'status'   => SdkVersion::STATUS_ACTIVE,
                    'admin_id' => 0,
                ]);
            }
        }
    }

    private function updateEnFields(array $versionList, $type)
    {
        foreach ($versionList as $version) {
            SdkVersion::query()
                ->where('type', $type)
                ->where('version', $version['version'])
                ->update([
                    'size_en'     => $version['size'],
                    'log_url_en'  => $version['changeLogURL'],
                    'demo_url_en' => $version['demoURL'],
                ]);
        }
    }
}
