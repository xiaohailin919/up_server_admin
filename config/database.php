<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', '127.0.0.1'),
            'port'        => env('DB_PORT', '3306'),
            'database'    => env('DB_DATABASE', 'forge'),
            'username'    => env('DB_USERNAME', 'forge'),
            'password'    => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],

        'bi' => [
            'driver'      => 'mysql',
            'host'        => env('BI_HOST', '127.0.0.1'),
            'port'        => env('BI_PORT', '3306'),
            'database'    => env('BI_DATABASE', 'forge'),
            'username'    => env('BI_USERNAME', 'forge'),
            'password'    => env('BI_PASSWORD', ''),
            'unix_socket' => env('BI_SOCKET', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'bi-greenplum' => [
            'driver'   => 'pgsql',
            'host'     => env('BI_GP_HOST', '127.0.0.1'),
            'port'     => env('BI_GP_PORT', '5432'),
            'database' => env('BI_GP_DATABASE', 'forge'),
            'username' => env('BI_GP_USERNAME', 'forge'),
            'password' => env('BI_GP_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'bi-greenplum-2' => [
            'driver'   => 'pgsql',
            'host'     => env('BI_GP_HOST_2', '127.0.0.1'),
            'port'     => env('BI_GP_PORT_2', '5432'),
            'database' => env('BI_GP_DATABASE_2', 'forge'),
            'username' => env('BI_GP_USERNAME_2', 'forge'),
            'password' => env('BI_GP_PASSWORD_2', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGODB_HOST'),
            'port'     => env('MONGODB_PORT'),
            'database' => env('MONGODB_DATABASE'),
            'username' => env('MONGODB_USERNAME'),
            'password' => env('MONGODB_PASSWORD'),
        ],

        'mongodb-2' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGODB_2_HOST'),
            'port'     => env('MONGODB_2_PORT'),
            'database' => env('MONGODB_2_DATABASE'),
            'username' => env('MONGODB_2_USERNAME'),
            'password' => env('MONGODB_2_PASSWORD'),
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'   => '',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
