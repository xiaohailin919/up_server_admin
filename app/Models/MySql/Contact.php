<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use DateTime;

class Contact extends Base {

    protected $table = 'contact';
    public $timestamps = true;
    protected $guarded = [
        'id'
    ];

    const STATUS_PROCESSED = 2;
    const STATUS_UNPROCESSED = 1;

    const ADMIN_DEFAULT = 0;

    const IM_TYPE_WECHAT = 1;
    const IM_TYPE_QQ = 2;
    const IM_TYPE_SKYPE = 3;

    const LANGUAGE_CHINESE = 'zh-cn';
    const LANGUAGE_ENGLISH = 'en';
    const LANGUAGE_JAPANESE = 'ja';

    private static $languageMap;
    private static $statusMap;

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

    public static function getLanguageMap(): array
    {
        return [
            self::LANGUAGE_CHINESE => 'Chinese',
            self::LANGUAGE_ENGLISH => 'English',
            self::LANGUAGE_JAPANESE => 'Japanese',
        ];
    }

    public static function getLanguageName($language): string
    {
        if (self::$languageMap == null) {
            self::$languageMap = self::getLanguageMap();
        }
        return self::$languageMap[$language] ?? '';
    }

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_UNPROCESSED => '未联系',
            self::STATUS_PROCESSED => '已联系'
        ];
    }

    public static function getStatusName($status)
    {
        if (self::$statusMap == null) {
            self::$statusMap = self::getStatusMap();
        }
        return self::$statusMap[$status] ?? '';
    }
}
