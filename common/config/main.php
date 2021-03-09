<?php
return [
    'name' => 'My Application',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['art', 'queue'],
    'language' => 'ru',
    'sourceLanguage' => 'en-US',
    'components' => [
        'art' => [
            'class' => 'artsoft\Art',
            'languages' => [
//              'en-US' => 'English',
                'ru' => 'Россия',
            ],
            'languageRedirects' => ['ru' => 'ru'],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'name' => 'art',
            'timeout' => '86400'
        ],
        'settings' => [
            'class' => 'artsoft\components\Settings'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'artsoft\components\User',
            'on afterLogin' => function ($event) {
                \artsoft\models\UserVisitLog::newVisitor($event->identity->id);
            }
        ],
        'db' => require __DIR__ . '/_db.php',
        'mailer' => require __DIR__ . '/_mailer.php',
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // DB connection component or its config 
            'ttr' => 5 * 60, // Максимальное время выполнения задания 
            'attempts' => 3, // Максимальное кол-во попыток
//            'tableName' => '{{%queue_push}}', // Table name
//            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,
            'as queueSchedule' => \artsoft\queue\JobSchedule::class,
        ],
    ],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'],
            'generators' => [
                'art-crud' => [
                    'class' => 'artsoft\generator\crud\Generator',
                    'templates' => [
                        'default' => '@artsoft/generator/crud/art-admin',
                    ]
                ],
                'job' => [
                    'class' => \yii\queue\gii\Generator::class,
                ],
            ],
        ],
    ],
];
