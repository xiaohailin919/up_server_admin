<?php
/**
 * Created by PhpStorm.
 * User: Liu
 * Date: 2019/7/11
 * Time: 4:04 PM
 */

namespace App\Models\MySql;


use App\Helpers\Format;

class Posts extends Base
{
    protected $table = 'posts';

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;

    const TYPE_NEWS   = 1;
    const TYPE_EVENT = 2;
    const TYPE_REPORT = 3;
    const TYPE_EMAIL  = 4;

    const LANGUAGE_CHINESE  = 'zh-cn';
    const LANGUAGE_ENGLISH  = 'en';
    const LANGUAGE_JAPANESE = 'ja';

    const POPULAR_YES = 2;
    const POPULAR_NO = 1;

    const EMAIL_NOT = 1;
    const EMAIL_SEND = 2;
    const EMAIL_SENDING = 3;

    public function getEventTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getPostTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['create_time', 'id'];

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DRAFT => 'Draft',
        ];
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_NEWS => __('common.posts.type.news'),
            self::TYPE_EVENT => __('common.posts.type.event'),
            self::TYPE_REPORT => __('common.posts.type.report'),
            self::TYPE_EMAIL => __('common.posts.type.email'),
        ];
    }

    public static function getLanguageMap(): array
    {
        return [
            self::LANGUAGE_CHINESE => __('common.posts.language.zh-cn'),
            self::LANGUAGE_ENGLISH => __('common.posts.language.en'),
            self::LANGUAGE_JAPANESE => __('common.posts.language.ja'),
        ];
    }

    public static function getEmailStatusMap(): array
    {
        return [
            self::EMAIL_NOT => 'Not send',
            self::EMAIL_SEND => 'Sent',
            self::EMAIL_SENDING => 'Sending',
        ];
    }

    public static function getStatusName($status)
    {
        $map = self::getStatusMap();
        return $map[$status] ?? '';
    }

    public static function getTypeName($type)
    {
        $map = self::getTypeMap();
        return $map[$type] ?? '';
    }

    public static function getLanguageName($lang)
    {
        $map = self::getLanguageMap();
        return $map[$lang] ?? '';
    }

    public static function getEmailStatusName($emailStatus): string
    {
        $map = self::getEmailStatusMap();
        return $map[$emailStatus] ?? '';
    }

    public static function getPopularList(): array
    {
        return [self::POPULAR_NO, self::POPULAR_YES];
    }
}