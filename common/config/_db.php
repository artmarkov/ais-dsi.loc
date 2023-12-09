<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => env('MYSQL_DSN'),
    'username' => env('MYSQL_USERNAME', 'username'),
    'password' => env('MYSQL_PASSWORD', 'password'),            
    'tablePrefix' => env('DB_TABLE_PREFIX'),
    'charset' => env('DB_CHARSET', 'utf8'),
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 3600,
];