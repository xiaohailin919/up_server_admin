<?php
namespace App\Helpers;

use App\Models\MySql\App;
use App\Models\MySql\AppTerm;
use App\Models\MySql\AppTermRelationship;
use App\Models\MySql\Network;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Segment;
use App\Models\MySql\Scenario;
use App\Models\MySql\Geo;
use App\Models\MySql\Unit;


class InputFilter {
    /**
     * 获取&清洗 Publisher ID
     *
     * @param  array $ids
     * @return array
     */
    public static function getPublisherIdsByIds(array $ids): array{
        $publishers = Publisher::query()
            ->whereIn('id', $ids)
            ->get(['id']);

        return empty($publishers) ? [] : array_column($publishers->toArray(), 'id');
    }

    /**
     * 获取&清洗 App ID
     *
     * @param  array $uuids
     * @return arary
     */
    public static function getAppIdsByUuids(array $uuids): array{
        $apps = App::query()
            ->whereIn('uuid', $uuids)
            ->get(['id']);

        return empty($apps) ? [] : array_column($apps->toArray(), 'id');
    }

    /**
     * 获取&清洗 App UUID
     *
     * @param  array $uuids
     * @return arary
     */
    public static function getAppUuidsByUuids(array $uuids): array{
        $apps = App::query()
            ->whereIn('uuid', $uuids)
            ->get(['uuid']);

        return empty($apps) ? [] : array_column($apps->toArray(), 'uuid');
    }

    /**
     * 获取&清洗 Placement UUID
     *
     * @param  array $uuids
     * @return array
     */
    public static function getPlacementIdsByUuids(array $uuids): array{
        if(empty($uuids)){
            return [];
        }
        $placements = Placement::query()
            ->whereIn('uuid', $uuids)
            ->get(['id']);

        return empty($placements) ? [] : array_column($placements->toArray(), 'id');
    }

    /**
     * 获取&清洗 Geo Short
     *
     * @param  array $shorts
     * @return array
     */
    public static function getGeoShrotsByShorts(array $shorts): array{
        $geos = Geo::query()
            ->whereIn('short', $shorts)
            ->get(['short']);

        return empty($geos) ? [] : array_column($geos->toArray(), 'short');
    }
}