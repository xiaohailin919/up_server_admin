<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class ReportImport extends Base
{
    protected $table = 'report_import';
    const TABLE = 'report_import';

    const STATUS_UPLOAD_FAILED  = 0;
    const STATUS_IMPORT_PENDING = 1;
    const STATUS_IMPORT_SUCCESS = 2;
    const STATUS_IMPORT_FAILED  = 3;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getImportTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 获取所有状态码映射配置
     * @return array
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_UPLOAD_FAILED  => '上传失败',
            self::STATUS_IMPORT_PENDING => '导入中',
            self::STATUS_IMPORT_SUCCESS => '导入成功',
            self::STATUS_IMPORT_FAILED  => '导入失败',
        ];
    }
}
