<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DataRole extends Base
{
    protected $table = 'data_role';

    const TYPE_ADMIN    = 1;
    const TYPE_PRODUCT  = 2;
    const TYPE_OPERATOR = 3;
    const TYPE_MARKET   = 4;
    const TYPE_TECH     = 5;
    const TYPE_BUSINESS = 6;
    const TYPE_OTHERS   = 9999;

    const DISPLAY_TYPE_BUSINESS = 1;
    const DISPLAY_TYPE_OPERATOR = 2;
    const DISPLAY_TYPE_OTHER    = 99;

    protected $guarded = ['id'];

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'data_role_user_relationship', 'role_id', 'user_id');
    }

    public function publishers(): MorphToMany
    {
        return $this->morphToMany(
            Publisher::class,
            'target',
            'data_role_user_permission',
            'target_id',
            'publisher_id'
        )->withPivot('display_type');
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_ADMIN    => '管理员',
            self::TYPE_PRODUCT  => '产品',
            self::TYPE_OPERATOR => '运营',
            self::TYPE_MARKET   => '市场',
            self::TYPE_TECH     => '技术',
            self::TYPE_BUSINESS => '商务',
            self::TYPE_OTHERS   => '其他',
        ];
    }

    public static function getTypeName($type): string
    {
        $map = self::getTypeMap();
        return $map[$type];
    }

}
