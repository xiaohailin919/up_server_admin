<?php
/**
 * 业务响应码
 */
return [
    /* 成功响应类 */
    0  => 'Success',
    1  => 'Create Success',
    2  => 'Accepted',
    3  => 'Non authoritative info',
    4  => 'No content',
    5  => 'Reset content',

    /* 权限错误类 */
    9001 => 'Login required',
    9002 => 'Session expired',
    9003 => 'Permission required',
    9004 => 'Csrf token error',
    9005 => 'Certificate param error',
    9006 => 'This operation is not available in test environment',

    /* 服务器错误类 */
    9993 => 'Remote service call failed',
    9994 => 'Logged-in user not found',
    9995 => 'Database operation failed',
    9996 => 'Not implemented',
    9997 => 'Service unavailable',
    9998 => 'Internal server error',
    9999 => 'Common server exception',

    /* 请求错误、资源错误类 */
    10000 => 'Parameter is error',
    10001 => 'Method not allowed',
    10002 => 'Resource is not available',
    10003 => 'Resource is not found',

    /* 业务错误类 - APP */
    11000 => 'App ID error',
    11001 => 'App UUID error',
    11002 => 'App name error',
    11003 => 'App type ID or App label ID error',
    11004 => 'App label ID error',
    11005 => 'The app type already exists',
    11006 => 'The app label already exists',
    11007 => 'App ID list contains apps belong to different label dimension',
    11008 => 'App label list contains labels belong to different parents',
    11009 => 'App ID is not exist',

    /* 业务错误类 - Placement */
    12000 => 'Placement ID error',
    12001 => 'Placement UUID error',
    12002 => 'Placement name error',
    12003 => 'Placement ID is not exists',

    /* 业务错误类 - Publisher */
    13000 => 'Publisher ID error',
    13001 => 'Publisher email is not define',

    /* 业务错误类 - Network */
    14000 => 'Network firm ID error',

    /* 业务错误类 - Strategy */

    /* 业务错误类 - Contents */
    16001 => 'Posts ID error',

    /* 业务错误类 - ADX */
    17000 => 'ADX demand not found',
];