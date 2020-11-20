<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute必须是合法的',
    'active_url'           => ':attribute不是有效URL',
    'after'                => ':attribute必须在:date以后的日期',
    'after_or_equal'       => ':attribute必须大于或等于:date',
    'alpha'                => ':attribute只能包含字母',
    'alpha_dash'           => ':attribute只能包含字母,数字和破折号',
    'alpha_num'            => ':attribute只能包含字母和数字',
    'array'                => ':attribute必须是一个数组',
    'before'               => ':attribute必须在:date以前的日期',
    'before_or_equal'      => ':attribute必须小于或等于:date',
    'between'              => [
        'numeric' => ':attribute必须介于:min和:max之间',
        'file'    => ':attribute必须介于:min和:max千字节之间',
        'string'  => ':attribute必须介于:min和:max字符之间',
        'array'   => ':attribute必须介于:min和:max项之间',
    ],
    'boolean'              => ':attribute必须为true或false',
    'confirmed'            => ':attribute确认不匹配',
    'date'                 => ':attribute不是一个有效的日期',
    'date_format'          => ':attribute与:format样式不匹配',
    'different'            => ':attribute和:other必须不一样',
    'digits'               => ':attribute必须是:digits数字',
    'digits_between'       => ':attribute必须介于:min和:max数字之间',
    'dimensions'           => ':attribute具有无效的图像维度',
    'distinct'             => ':attribute字段有一个重复的值',
    'email'                => ':attribute必须是一个有效的邮箱地址',
    'exists'               => '所选的:attribute无效',
    'file'                 => ':attribute必须是一个文件',
    'filled'               => ':attribute字段必须有一个值',
    'image'                => ':attribute必须是一个图像',
    'in'                   => '所选的:attribute无效',
    'in_array'             => ':attribute字段不存在于:other中',
    'integer'              => ':attribute必须是一个整数',
    'ip'                   => ':attribute必须是一个有效的IP地址',
    'ipv4'                 => ':attribute必须是一个有效的IPv4地址',
    'ipv6'                 => ':attribute必须是一个有效的IPv6地址',
    'json'                 => ':attribute必须是一个有效的JSON字符串',
    'max'                  => [
        'numeric' => ':attribute不能大于:max',
        'file'    => ':attribute不能大于:max千字节',
        'string'  => ':attribute不能大于:max字符',
        'array'   => ':attribute不能大于:max项',
    ],
    'mimes'                => ':attribute必须是一个::values类型文件',
    'mimetypes'            => ':attribute必须是一个::values类型文件',
    'min'                  => [
        'numeric' => ':attribute不能小于:min',
        'file'    => ':attribute不能小于:min千字节',
        'string'  => ':attribute不能小于:min字符',
        'array'   => ':attribute不能小于:min项',
    ],
    'not_in'               => '所选的:attribute无效',
    'numeric'              => ':attribute必须是一个数字',
    'present'              => ':attribute字段必填',
    'regex'                => ':attribute样式无效',
    'required'             => '必填项!',
    'required_if'          => ':attribute字段必填(当:other是:value时)',
    'required_unless'      => ':attribute字段必填(若:other在:values里面则非必填)',
    'required_with'        => ':attribute字段必填(当:values出现时)',
    'required_with_all'    => ':attribute字段必填(当:values出现时)',
    'required_without'     => ':attribute字段必填(当:values不出现时)',
    'required_without_all' => ':attribute字段必填(当没有:values出现时)',
    'same'                 => ':attribute与:other必须匹配',
    'size'                 => [
        'numeric' => ':attribute必须是:size',
        'file'    => ':attribute必须是:size千字节',
        'string'  => ':attribute必须是:size字符',
        'array'   => ':attribute必须是:size项',
    ],
    'string'               => ':attribute必须是一个字符串',
    'timezone'             => ':attribute必须是一个有效的区域',
    'unique'               => ':attribute已存在',
    'uploaded'             => ':attribute上传失败',
    'url'                  => ':attribute样式无效',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'not_exists' => '所选的:attribute无效',
        'lower_case' => ':attribute不能包含大写字符',
        'channel_id' => '渠道ID'
    ],

];
