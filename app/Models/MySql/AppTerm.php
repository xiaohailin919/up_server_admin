<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use DateTime;
use Illuminate\Support\Facades\Log;

class AppTerm extends Base
{

    const TYPE_APP_TYPE = 1;
    const TYPE_LABEL_PARENT = 2;
    const TYPE_LABEL_CHILD = 3;

    const PARENT_NONE = 0;

    const STATUS_STOP = 1;
    const STATUS_ACTIVE = 3;

    protected $table = 'app_term';

    protected $guarded = ['id'];

    /**
     * @var bool 是否启用默认时间戳
     */
    public $timestamps = true;

    /**
     * 数据库时间格式转换
     *
     * @param DateTime|int $value 从数据库读取则为 Timestamp 类型，框架写入则为 int 类型
     * @return DateTime|false|int|string
     */
    public function fromDateTime($value) {
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 获取各类型对应的 ID 列表
     *
     * @return array|array[]
     */
    public static function getTypeIdListMap(): array {
        $res = [
            self::TYPE_APP_TYPE => [],
            self::TYPE_LABEL_PARENT => [],
            self::TYPE_LABEL_CHILD => [],
        ];
        $records = self::query()->where('status', self::STATUS_ACTIVE)->get(['id', 'type'])->toArray();
        foreach ($records as $record) {
            $res[$record['type']][] = $record['id'];
        }
        return $res;
    }

    /**
     * 获取复制标签嵌套的树形列表
     * 如：
     * {
     *     "id": 1,
     *     "name": "parentLabel",
     *     "children": [
     *         {
     *             "id": 2,
     *             "name": "childLabel"
     *         }
     *     ]
     * }
     * @return array
     */
    public static function getLabelParentChildrenMap(): array {
        $labels = self::query()->where('status', self::STATUS_ACTIVE)
            ->where('type', '!=', self::TYPE_APP_TYPE)
            ->get()->toArray();
        $parentLabels = array_where($labels, static function ($value) {
            return $value['type'] === self::TYPE_LABEL_PARENT;
        });
        foreach ($parentLabels as $i => $parentLabel) {
            $childLabels = array_values(array_where($labels, static function ($value) use ($parentLabel) {
                return $value['parent_id'] === $parentLabel['id'];
            }));
            $parentLabel['children'] = $childLabels;
            $parentLabels[$i] = $parentLabel;
        }
        return $parentLabels;
    }

    /**
     * 获取复制标签嵌套的树形 ID 列表
     * 如：
     * {
     *     1: [2, 3, 4],
     *     2: [5, 6, 7],
     *     3: [8]
     * }
     * @return array
     */
    public static function getLabelParentChildrenIdMap(): array {
        $labels = self::query()->where('status', self::STATUS_ACTIVE)
            ->where('type', '!=', self::TYPE_APP_TYPE)
            ->get(['id', 'parent_id', 'type'])->toArray();
        $parentLabels = array_where($labels, static function ($value) {
            return $value['type'] === self::TYPE_LABEL_PARENT;
        });
        $res = [];
        foreach ($parentLabels as $parentLabel) {
            $res[$parentLabel['id']] = [];
            $childLabels = array_values(array_where($labels, static function ($value) use ($parentLabel) {
                return $value['parent_id'] === $parentLabel['id'];
            }));
            foreach ($childLabels as $childLabel) {
                $res[$parentLabel['id']][] = $childLabel['id'];
            }
        }
        return $res;
    }

    /**
     * 临时
     */
    public static function getAppTypeMap() {
        $types = self::query()->where('type', self::TYPE_APP_TYPE)->where('status', self::STATUS_ACTIVE)
            ->get(['id', 'name'])->toArray();
        return array_column($types, 'name', 'id');
    }
}