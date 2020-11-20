<?php

namespace App\Models\MySql;


use App\Helpers\Format;
use DateTime;

class PostsTerm extends Base
{
    protected $table = 'posts_term';
    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = [
        self::UPDATED_AT,
        self::CREATED_AT
    ];

    const POPULAR_NOT = 1;
    const POPULAR_YES = 2;

    const STATUS_ACTIVE = 3;
    const STATUS_BLOCK = 1;
    const TYPE_CATEGORY = 1;
    const TYPE_TAG = 2;

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

    public function freshTimestamp()
    {
        return date('Y-m-d H:i:s');
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_CATEGORY => '分类',
            self::TYPE_TAG => '标签',
        ];
    }

    public static function getStatusMap(): array {
        return [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_BLOCK => '删除',
        ];
    }

    public static function getTypeName(int $type)
    {
        $map = self::getTypeMap();
        return $map[$type];
    }

    public static function getCategoriesRecords() {
        $categories = self::query()
            ->where('type', self::TYPE_CATEGORY)
            ->where('status', self::STATUS_ACTIVE)
            ->get();
        for ($i = 0, $iMax = count($categories); $i < $iMax; $i++) {
            $categories[$i]['slug'] = str_replace('-', ' ', $categories[$i]['slug']);
        }
        return $categories;
    }

    public static function getTagsRecords() {
        $tags = self::query()
            ->where('type', self::TYPE_TAG)
            ->where('status', self::STATUS_ACTIVE)
            ->get();
        for ($i = 0, $iMax = count($tags); $i < $iMax; $i++) {
            $tags[$i]['slug'] = str_replace('-', ' ', $tags[$i]['slug']);
        }
        return $tags;
    }
}