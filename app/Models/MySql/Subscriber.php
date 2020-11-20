<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use DateTime;

class Subscriber extends Base {
    protected $table = 'subscriber';

    const STATUS_SUBSCRIBE = 1;
    const STATUS_UNSUBSCRIBE = 2;

    const SUBSCRIBE_TYPE_INDEX = 1;
    const SUBSCRIBE_TYPE_TEST = 2;
    const SUBSCRIBE_TYPE_MANUAL = 3;

    const LANGUAGE_CHINESE  = 'zh-cn';
    const LANGUAGE_ENGLISH  = 'en';
    const LANGUAGE_JAPANESE = 'ja';

    const POST_CONTENT_KEY = 'post_content';
//    const SUBSCRIBE_SUCCESS_KEY = 'subscribe_success';

    const NO_SUBSCRIBER_SIGN = 'no subscriber';

    const UNSUBSCRIBE_TOKEN = 'TopOn20200219';

    protected $guarded = ['id'];

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

    public static function getStatusList(): array {
        return [self::STATUS_SUBSCRIBE, self::STATUS_UNSUBSCRIBE];
    }

    public static function getSubscriberTypeList() :array {
        return [self::SUBSCRIBE_TYPE_INDEX, self::SUBSCRIBE_TYPE_TEST, self::SUBSCRIBE_TYPE_MANUAL];
    }

    public static function getLanguageList(): array {
        return [self::LANGUAGE_CHINESE, self::LANGUAGE_ENGLISH, self::LANGUAGE_JAPANESE];
    }
}
