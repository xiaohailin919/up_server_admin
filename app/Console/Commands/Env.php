<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Env extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:env {env=vg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create .env file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $env = $this->argument('env');
        self::createEnv($env);
    }

    private static function createEnv($env)
    {
        $file = __DIR__ . '/../../../.env';

        $dir = (dirname($file));

        if (!is_writable($dir)) {
            exit($dir . ' 没有写权限');
        }

        $str = '';
        $config = self::getConfigByEnv($env);

        foreach ($config as $k => $v) {
            $str .= $k . '=' . $v . "\n";
        }

        if (!file_put_contents($file, $str)) {
            exit('写入文件失败');
        }

        exit('success');
    }

    private static function getConfigByEnv($env)
    {
        $functionDict = [
            'test' => 'testConfig',
            'vg'   => 'vgConfig',
            'sg'   => 'sgConfig',
        ];

        if (!in_array($env, array_keys($functionDict))) {
            $env = 'test';
        }

        $config = call_user_func(array(__CLASS__, $functionDict[$env]));

//        $app = $config['app'];
        $db = $config['db'];
//        $mongo = $config['mongo'];
        $api = $config['api'];
        $mailService = $config['mail_service'];
        $biService = $config['bi'];
//        $waterfallHost = $config['waterfall_host'];

        return array(
            // System
            'APP_NAME'              => '"TopOn Admin"',
            'APP_ENV'               => $config['app_env'],
            'APP_DEBUG'             => $config['app_debug'],
            'APP_KEY'               => $config['app_key'],
            'APP_TIMEZONE'          => $config['app_timezone'],
            'XXX_ENV'               => $env,
            'LOGIN_TOKEN_FOR_ADMIN' => $config['login_token_for_admin'],
            'JWT_SECRET'            => $config['jwt_secret'],
            'JWT_TTL'               => $config['jwt_ttl'],

            // DB RDS
            'DB_CONNECTION' => $db['conn'],
            'DB_HOST'       => $db['host'],
            'DB_PORT'       => $db['port'],
            'DB_DATABASE'   => $db['database'],
            'DB_USERNAME'   => $db['username'],
            'DB_PASSWORD'   => $db['password'],
            // DB RDS BI
            'BI_HOST'     => $db['bi_host'],
            'BI_PORT'     => $db['bi_port'],
            'BI_DATABASE' => $db['bi_database'],
            'BI_USERNAME' => $db['bi_username'],
            'BI_PASSWORD' => $db['bi_password'],
            // DB mongodb
            'MONGODB_HOST'     => $config['mongodb_host'],
            'MONGODB_PORT'     => $config['mongodb_port'],
            'MONGODB_DATABASE' => $config['mongodb_database'],
            'MONGODB_USERNAME' => $config['mongodb_username'],
            'MONGODB_PASSWORD' => $config['mongodb_password'],
            // DB mongodb-2
            'MONGODB_2_HOST'     => $config['mongodb_2_host'],
            'MONGODB_2_PORT'     => $config['mongodb_2_port'],
            'MONGODB_2_DATABASE' => $config['mongodb_2_database'],
            'MONGODB_2_USERNAME' => $config['mongodb_2_username'],
            'MONGODB_2_PASSWORD' => $config['mongodb_2_password'],

            'REDIS_HOST' => $config['redis_host'],
            'REDIS_PORT' => $config['redis_port'],

            // Mail Service
            'MAIL_SERVICE_URL'   => $mailService['mail_service_url'],
            'MAIL_SERVICE_UUID0' => $mailService['mail_service_uuid0'],
            'MAIL_SERVICE_UUID1' => $mailService['mail_service_uuid1'],
            'MAIL_SERVICE_UUID2' => $mailService['mail_service_uuid2'],
            'MAIL_SERVICE_UUID3' => 'abcdefghigklmnopqrstuvwxyz33333333333333',
            'MAIL_SUPPORT'       => $config['MAIL_SUPPORT'],

            'REPORT_API' => $api['report'],
            'DN_UP_APP' => $api['dn_up_app'],
            'DN_AM_DEV' => $api['dn_am_dev'],

            // BI API
            'BI_SERVICE_DEDUCTION_LIST'     => $biService['BI_SERVICE_DEDUCTION_LIST'],
            'BI_SERVICE_DEDUCTION'          => $biService['BI_SERVICE_DEDUCTION'],
            'BI_SERVICE_DEDUCTION_RET'      => $biService['BI_SERVICE_DEDUCTION_RET'],
            'BI_SERVICE_IMPORT'             => $biService['BI_SERVICE_IMPORT'],
            'BI_SERVICE_IMPORT_LOG_LIST'    => $biService['BI_SERVICE_IMPORT_LOG_LIST'],
            'BI_SERVICE_ADMIN_REPORT'       => $biService['BI_SERVICE_ADMIN_REPORT'],
            'BI_SERVICE_ADMIN_REPORT_V2'    => $config['BI_SERVICE_ADMIN_REPORT_V2'],
            'BI_SERVICE_ADMIN_CHART_REPORT' => $biService['BI_SERVICE_ADMIN_CHART_REPORT'],
            'BI_SERVICE_ADMIN_TC_REPORT'    => $config['BI_SERVICE_ADMIN_TC_REPORT'],

            // Waterfall Service
            'WATERFALL_HOST' => $config['waterfall_host'],

            // BI GP
            'BI_GP_HOST'     => $config['BI_GP_HOST'],
            'BI_GP_PORT'     => $config['BI_GP_PORT'],
            'BI_GP_DATABASE' => $config['BI_GP_DATABASE'],
            'BI_GP_USERNAME' => $config['BI_GP_USERNAME'],
            'BI_GP_PASSWORD' => $config['BI_GP_PASSWORD'],

            // BI GP 2
            'BI_GP_HOST_2'     => $config['BI_GP_HOST_2'],
            'BI_GP_PORT_2'     => $config['BI_GP_PORT_2'],
            'BI_GP_DATABASE_2' => $config['BI_GP_DATABASE_2'],
            'BI_GP_USERNAME_2' => $config['BI_GP_USERNAME_2'],
            'BI_GP_PASSWORD_2' => $config['BI_GP_PASSWORD_2'],

            // 队列驱动
            'QUEUE_DRIVER' => $config['QUEUE_DRIVER'],

            // 缓存驱动
            'CACHE_DRIVER' => $config['CACHE_DRIVER'],

            // 邮件发送
            'MAIL_HOST'         => 'smtp.exmail.qq.com',
            'MAIL_PORT'         => '465',
            'MAIL_USERNAME'     => 'newsletter@toponad.com',
            'MAIL_PASSWORD'     => 'TopOn@20200820',
            'MAIL_ENCRYPTION'   => 'ssl',
            'MAIL_FROM_ADDRESS' => 'newsletter@toponad.com',
            'MAIL_FROM_NAME'    => 'newsletter@toponad.com',

            // Other
            'LOG_PATH'           => $config['log_path'],
            'FILE_UPLOAD_PATH'   => $config['FILE_UPLOAD_PATH'],
            'DN_CDN'             => $config['DN_CDN'],
            'CHANNEL_233_HOST'   => $config['CHANNEL_233_HOST'],
            'CHANNEL_DLADS_HOST' => $config['CHANNEL_DLADS_HOST'],
            'TOPON_HOST'         => $config['TOPON_HOST'],
        );
    }

    private static function vgConfig()
    {
        $app = array(
            'key' => 'base64:ubeWW9pqDRXt/hgSXRZbnWyzy68opzOU5oedC31N+P8=',
            'timezone' => 'PRC',
            'env' => 'production',
            'debug' => 'false',
            'log_path' => '/data/spserver/logs/runtime/admin.uparpu.com/runtime.log',
        );

        $db = [
            'conn' => 'mysql',
            'host' => 'sp-uparpumaster.c5yzcdreb1xr.us-east-1.rds.amazonaws.com',
            'port' => '3306',
            'database' => 'uparpu_main',
            'username' => 'spUparpu',
            'password' => 'Vi62PAZH2h1CBP5b',
            // BI
            'bi_host' => 'sp-uparpumaster.c5yzcdreb1xr.us-east-1.rds.amazonaws.com',
            'bi_port' => '3306',
            'bi_database' => 'bi',
            'bi_username' => 'bi_rw',
            'bi_password' => 'yXTuh9v6FC3GN3J',
        ];

        $mongoDb = [
            'host' => 'master.mongodb.uparpu.com',
            'port' => 27017,
            'database' => 'uparpu_main',
            'username' => '',
            'password' => '',
        ];

        $api = [
            'report' => 'https://app.toponad.com/',
            'dn_up_app' => 'https://app.toponad.com/',
            'dn_am_dev' => 'https://dev.automediation.com/',
        ];

        $mailService = [
            'mail_service_url' => 'http://10.29.1.25:3004/v1/send_mail',
            'mail_service_uuid0' => 'abcdefghigklmnopqrstuvwxyz00000000000000', // publisher@salmonads.com
            'mail_service_uuid1' => 'abcdefghigklmnopqrstuvwxyz33333333333333', // developer@toponad.com
            'mail_service_uuid2' => 'abcdefghigklmnopqrstuvwxyz22222222222222', // developer@automediation.com
        ];

        $biService = [
            'BI_SERVICE_ADMIN_REPORT' => 'http://bi-internal.uparpu.com/api/v2/bi/search/admin_report',
            'BI_SERVICE_ADMIN_CHART_REPORT' => 'http://bi-internal.uparpu.com/api/v2/bi/search/admin_chart_report',
            'BI_SERVICE_DEDUCTION_LIST' => 'http://bi-internal.uparpu.com/api/v1/bi/report_assignment_list',
            'BI_SERVICE_DEDUCTION' => 'http://bi-internal.uparpu.com/api/v1/bi/report_assignment',
            'BI_SERVICE_DEDUCTION_RET' => 'http://bi-internal.uparpu.com/api/v1/bi/report_assignment_rerun',
            'BI_SERVICE_IMPORT' => 'http://bi-internal.uparpu.com/api/v1/bi/report_import',
            'BI_SERVICE_IMPORT_LOG_LIST' => 'http://bi-internal.uparpu.com/api/v1/bi/report_import_log',
        ];

        return [
//            'app' => $app,
            'db' => $db,
//            'mongo' => $mongoDb,
            'api' => $api,
            'mail_service' => $mailService,
            'bi' => $biService,
            "waterfall_host" => "10.29.1.232:8065",

            #########
            // 系统
            'app_key'               => 'base64:ubeWW9pqDRXt/hgSXRZbnWyzy68opzOU5oedC31N+P8=',
            'app_timezone'          => 'PRC',
            'app_env'               => 'production',
            'app_debug'             => 'false',
            'log_path'              => '/data/spserver/logs/runtime/admin.uparpu.com/runtime.log',
            'login_token_for_admin' => 'AhFtsHsMqXwOa0loacbuqu9Im5U8xE06',
            'jwt_secret'            => 'bztcxNIHTep6r1RboGtOOBlb8a8w2MzI4jWFVWuGHp4DbYZz3vdi0LuYtpLP8G9A',
            'jwt_ttl'               => '20160',

            // mongodb
            'mongodb_host'     => 'master.mongodb.uparpu.com',
            'mongodb_port'     => 27017,
            'mongodb_database' => 'uparpu_main',
            'mongodb_username' => '',
            'mongodb_password' => '',
            // mongodb-2
            'mongodb_2_host'     => '10.29.1.201',
            'mongodb_2_port'     => 27017,
            'mongodb_2_database' => 'uparpu_main',
            'mongodb_2_username' => '',
            'mongodb_2_password' => '',

            // redis
            'redis_host'     => '10.29.1.197',
            'redis_port'     => '6379',

            // BI greenplum
            'BI_GP_HOST'     => 'gp-internal.toponad.com',
            'BI_GP_PORT'     => '15432',
            'BI_GP_DATABASE' => 'bi',
            'BI_GP_USERNAME' => 'topon',
            'BI_GP_PASSWORD' => 'pfr3em6E2hmpUXv',

            // BI greenplum 2
            'BI_GP_HOST_2'     => 'v2gp-internal.toponad.com',
            'BI_GP_PORT_2'     => '15432',
            'BI_GP_DATABASE_2' => 'bi',
            'BI_GP_USERNAME_2' => 'bi_rw',
            'BI_GP_PASSWORD_2' => 'T2YKQYFXK^TK0!J46%R',

            // BI API
            'BI_SERVICE_ADMIN_REPORT_V2' => 'http://bi-internal.uparpu.com/api/v2/bi/search/admin_report_new',
            'BI_SERVICE_ADMIN_TC_REPORT' => 'http://bi-internal.uparpu.com/api/v3/bi/search/tc/report',

            // 队列驱动
            'QUEUE_DRIVER' => 'redis',

            // 缓存驱动
            'CACHE_DRIVER' => 'redis',

            // other
            'FILE_UPLOAD_PATH'   => '/data/spserver/web/img.uparpu.com',
            'DN_CDN'             => 'http://img.toponad.com',
            'MAIL_SUPPORT'       => 'support@toponad.com',
            'CHANNEL_233_HOST'   => "admin.233.toponad.com",
            'CHANNEL_DLADS_HOST' => "admin.dlads.toponad.com",
            'TOPON_HOST'         => "admin.uparpu.com"
        ];
    }

    private static function sgConfig()
    {
        self::vgConfig();
    }

    private static function testConfig()
    {
        $app = array(
            'key' => 'base64:ubeWW9pqDRXt/hgSXRZbnWyzy68opzOU5oedC31N+P8=',
            'timezone' => 'PRC',
            'env' => 'test',
            'debug' => 'true',
            'log_path' => '/data/spserver/logs/runtime/admin.uparpu.com/runtime.log',
        );

        $db = [
            'conn' => 'mysql',
            'host' => '10.29.1.203',
            'port' => '3308',
            'database' => 'uparpu_main_3',
            'username' => 'root',
            'password' => '111111',
            // BI
            'bi_host' => '10.29.1.203',
            'bi_port' => '3308',
            'bi_database' => 'bi',
            'bi_username' => 'root',
            'bi_password' => '111111',
        ];

        $mongoDb = [
            'host' => '127.0.0.1',
            'port' => 27017,
            'database' => 'uparpu_main',
            'username' => '',
            'password' => '',
        ];

        $api = [
            'report' => 'http://test.app.uparpu.com/',
            'dn_up_app' => 'http://test.app.toponad.com/',
            'dn_am_dev' => 'http://test.dev.automediation.com/',
        ];

        $mailService = [
            'mail_service_url' => 'http://127.0.0.1:3004/v1/send_mail',
            'mail_service_uuid0' => 'abcdefghigklmnopqrstuvwxyz00000000000000',
            'mail_service_uuid1' => 'abcdefghigklmnopqrstuvwxyz33333333333333',
            'mail_service_uuid2' => 'abcdefghigklmnopqrstuvwxyz22222222222222',
        ];

        $biService = [
            'BI_SERVICE_ADMIN_REPORT' => 'http://test.bi.uparpu.com/api/v2/bi/search/admin_report',
            'BI_SERVICE_ADMIN_CHART_REPORT' => 'http://test.bi.uparpu.com/api/v2/bi/search/admin_chart_report',
            'BI_SERVICE_DEDUCTION_LIST' => 'http://test.bi.uparpu.com/api/v1/bi/report_assignment_list',
            'BI_SERVICE_DEDUCTION' => 'http://test.bi.uparpu.com/api/v1/bi/report_assignment',
            'BI_SERVICE_DEDUCTION_RET' => 'http://test.bi.uparpu.com/api/v1/bi/report_assignment_rerun',
            'BI_SERVICE_IMPORT' => 'http://test.bi.uparpu.com/api/v1/bi/report_import',
            'BI_SERVICE_IMPORT_LOG_LIST' => 'http://test.bi.uparpu.com/api/v1/bi/report_import_log',
        ];

        return [
//            'app' => $app,
            'db' => $db,
//            'mongo' => $mongoDb,
            'api' => $api,
            'mail_service' => $mailService,
            'bi' => $biService,
            'waterfall_host' => "127.0.0.1:8086",

            #########
            // 系统
            'app_key'               => 'base64:ubeWW9pqDRXt/hgSXRZbnWyzy68opzOU5oedC31N+P8=',
            'app_timezone'          => 'PRC',
            'app_env'               => 'test',
            'app_debug'             => 'true',
            'log_path'              => '/data/spserver/logs/runtime/admin.uparpu.com/runtime.log',
            'login_token_for_admin' => 'HahrTWMKuk6cEdED',
            'jwt_secret'            => 'bztcxNIHTep6r1RboGtOOBlb8a8w2MzI4jWFVWuGHp4DbYZz3vdi0LuYtpLP8G9A',
            'jwt_ttl'               => '20160',

            // mongodb
            'mongodb_host'     => '127.0.0.1',
            'mongodb_port'     => 27017,
            'mongodb_database' => 'uparpu_main',
            'mongodb_username' => '',
            'mongodb_password' => '',
            // mongodb-2
            'mongodb_2_host'     => '127.0.0.1',
            'mongodb_2_port'     => 27017,
            'mongodb_2_database' => 'uparpu_main',
            'mongodb_2_username' => '',
            'mongodb_2_password' => '',

            // redis
            'redis_host'     => '127.0.0.1',
            'redis_port'     => '6379',

            // BI greenplum
            'BI_GP_HOST'     => '3.226.190.155',
            'BI_GP_PORT'     => '15432',
            'BI_GP_DATABASE' => 'topon',
            'BI_GP_USERNAME' => 'mob_dgadmin',
            'BI_GP_PASSWORD' => 'XHtm42w2L6XwanW',

            // BI greenplum 2
            'BI_GP_HOST_2'     => '3.226.190.155',
            'BI_GP_PORT_2'     => '15432',
            'BI_GP_DATABASE_2' => 'topon',
            'BI_GP_USERNAME_2' => 'mob_dgadmin',
            'BI_GP_PASSWORD_2' => 'XHtm42w2L6XwanW',

            // BI API
            'BI_SERVICE_ADMIN_REPORT_V2' => 'http://test.bi.uparpu.com/api/v2/bi/search/admin_report_new',
            'BI_SERVICE_ADMIN_TC_REPORT' => 'http://bi-internal.uparpu.com/api/v3/bi/search/tc/report',

            // 队列驱动
            'QUEUE_DRIVER' => 'redis',

            // 缓存驱动
            'CACHE_DRIVER' => 'file',

            // other
            'FILE_UPLOAD_PATH'   => '/data/spserver/web/img.uparpu.com',
            'DN_CDN'             => 'http://test.img.toponad.com',
            'MAIL_SUPPORT'       => 'sheben@toponad.com',
            'CHANNEL_233_HOST'   =>  "test.admin.233.toponad.com",
            'CHANNEL_DLADS_HOST' =>  "test.admin.dlads.toponad.com",
            'TOPON_HOST'         =>  "test.admin.uparpu.com",
        ];
    }
}