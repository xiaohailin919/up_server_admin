<?php

namespace App\Models\MySql;

use App\Helpers\InputFilter;

class AdxBwList extends Base
{
    protected $table = 'adx_bw_list';

    // 黑白名单
    const TYPE_WHITELIST = 1;
    const TYPE_BLACKLIST = 2;

    // 维度
    const DIMENSION_APP_AREA                = 7;
    const DIMENSION_APP                     = 6;
    const DIMENSION_PUBLISHER_PLATFORM_AREA = 5;
    const DIMENSION_PUBLISHER_PLATFORM      = 4;
    const DIMENSION_PUBLISHER_AREA          = 3;
    const DIMENSION_PUBLISHER               = 2;
    const DIMENSION_DEFAULT                 = 1;

    // 状态
    const STATUS_STOP   = 1;
    const STATUS_ACTIVE = 3;

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['create_time', 'id'];

    /**
     * 获取状态Map
     *
     * @return array|string[]
     */
    public static function getDimensionMap(): array{
        return [
            self::DIMENSION_APP_AREA                => 'App + Area',
            self::DIMENSION_APP                     => 'App',
            self::DIMENSION_PUBLISHER_PLATFORM_AREA => 'Publisher + Platform + Area',
            self::DIMENSION_PUBLISHER_PLATFORM      => 'Publisher + Platform',
            self::DIMENSION_PUBLISHER_AREA          => 'Publisher + Area',
            self::DIMENSION_PUBLISHER               => 'Publisher',
            self::DIMENSION_DEFAULT                 => 'Default',
        ];
    }

    /**
     * 获取状态Map
     *
     * @return array|string[]
     */
    public static function getStatusMap(): array{
        return [
            self::BILLING_BASIS_NURL       => 'Stop',
            self::BILLING_BASIS_IMPRESSION => 'Active',
        ];
    }

    /**
     * 生成 dimension
     *
     * @param  array $data
     * @return array
     */
    public function generateDimensions($data){
        $dimension  = $data['dimension'];
        $dimensions = [];
        switch($dimension){
            case self::DIMENSION_APP_AREA:
                $appIds    = json_decode($data['app_id'], true);
                $appIds    = InputFilter::getAppIdsByUuids($appIds);
                $geoShorts = json_decode($data['geo_short'], true);
                foreach($appIds as $appId){
                    foreach($geoShorts as $geoShort){
                        $dimensions[] = [
                            'publisher_id' => '0',
                            'platform'     => '0',
                            'app_id'       => $appId,
                            'geo_short'    => $geoShort,
                        ];
                    }
                }
                break;
            case self::DIMENSION_APP:
                $appIds = json_decode($data['app_id'], true);
                $appIds = InputFilter::getAppIdsByUuids($appIds);
                foreach($appIds as $appId){
                    $dimensions[] = [
                        'publisher_id' => '0',
                        'platform'     => '0',
                        'app_id'       => $appId,
                        'geo_short'    => '',
                    ];
                }
                break;
            case self::DIMENSION_PUBLISHER_PLATFORM_AREA:
                $publisherIds = json_decode($data['publisher_id'], true);
                $platforms    = json_decode($data['platform'], true);
                $geoShorts    = json_decode($data['geo_short'], true);
                foreach($publisherIds as $publisherId){
                    foreach($platforms as $platform){
                        foreach($geoShorts as $geoShort){
                            $dimensions[] = [
                                'publisher_id' => $publisherId,
                                'platform'     => $platform,
                                'app_id'       => '0',
                                'geo_short'    => $geoShort,
                            ];
                        }
                    }
                }
                break;
            case self::DIMENSION_PUBLISHER_PLATFORM:
                $publisherIds = json_decode($data['publisher_id'], true);
                $platforms    = json_decode($data['platform'], true);
                foreach($publisherIds as $publisherId){
                    foreach($platforms as $platform){
                        $dimensions[] = [
                            'publisher_id' => $publisherId,
                            'platform'     => $platform,
                            'app_id'       => 0,
                            'geo_short'    => '',
                        ];
                    }
                }
                break;
            case self::DIMENSION_PUBLISHER_AREA:
                $publisherIds = json_decode($data['publisher_id'], true);
                $geoShorts    = json_decode($data['geo_short'], true);
                foreach($publisherIds as $publisherId){
                    foreach($geoShorts as $geoShort){
                        $dimensions[] = [
                            'publisher_id' => $publisherId,
                            'platform'     => '0',
                            'app_id'       => '0',
                            'geo_short'    => $geoShort,
                        ];
                    }
                }
                break;
            case self::DIMENSION_PUBLISHER:
                $publisherIds = json_decode($data['publisher_id'], true);
                foreach($publisherIds as $publisherId){
                    $dimensions[] = [
                        'publisher_id' => $publisherId,
                        'platform'     => '0',
                        'app_id'       => '0',
                        'geo_short'    => '',
                    ];
                }
                break;
            default:
                $dimensions[] = [
                    'publisher_id' => '0',
                    'platform'     => '0',
                    'app_id'       => '0',
                    'geo_short'    => '',
                ];
                break;
        }

        return $dimensions;
    }

    /**
     * 检查 dimension 是否重复
     *
     * @param  array $dimensions
     * @param  int   $excludeParentId
     * @return array
     */
    public function checkDimensions($dimensions, $excludeParentId = 0){
        $existsDimensions = [];
        foreach($dimensions as $dim){
            $query = $this->query()
                ->where($dim)
                ->where('status', '<', AdxBwList::STATUS_DELETE);
            if($excludeParentId > 0){
                $query->where('parent_id', '!=', $excludeParentId);
            }
            $exists = $query->exists();
            if($exists){
                $existsDimensions[] = $dim;
            }
        }

        return $existsDimensions;
    }

    /**
     * 保存 子级数据
     *
     * @param  array $data
     * @param  array $dimensions
     * @return bool
     */
    public function saveChildren($data, $dimensions){
        $data['parent_id'] = $data['id'];
        unset($data['id']);

        $dateTime = date('Y-m-d H:i:s');
        foreach($dimensions as $dim){
            $adxBwList = array_merge($data, $dim);
            $exists = $this->query()
                ->where($dim)
                ->where('parent_id', $data['parent_id'])
                ->exists();
            if($exists){
                unset($adxBwList['create_time']);
                $adxBwList['update_time'] = $dateTime;
                $this->query()
                    ->where($dim)
                    ->where('parent_id', $data['parent_id'])
                    ->update($adxBwList);
            }else{
                $adxBwList['create_time'] = $dateTime;
                $adxBwList['update_time'] = $dateTime;
                $this->query()
                    ->create($adxBwList);
            }
        }

        // update不再当前版本下的数据
        $this->query()
            ->where('parent_id', $data['parent_id'])
            ->where('update_time', '<', $dateTime)
            ->update(['status' => self::STATUS_DELETE]);

        return true;
    }
}
