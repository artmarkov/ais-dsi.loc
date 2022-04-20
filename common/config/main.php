<?php
Yii::$container->set(\yii\widgets\DetailView::class, [
    'options' => ['class' => 'table detail-view'],
    'template' => '<tr><th style="width:30%;text-align:right;" {captionOptions}>{label}</th><td {contentOptions}>{value}</td></tr>',
]);

Yii::$container->set(\kartik\date\DatePicker::class, [
    'type' => \kartik\date\DatePicker ::TYPE_INPUT,
    'options' => ['placeholder' => ''],
    'convertFormat' => true,
    'pluginOptions' => [
        'format' => 'dd.MM.yyyy',
        'autoclose' => true,
        'weekStart' => 1,
        'startDate' => '01.01.1930',
        'endDate' => '01.01.2030',
        'todayBtn' => 'linked',
        'todayHighlight' => true,
    ]
]);

Yii::$container->set(\kartik\datetime\DateTimePicker::class, [
    'type' => \kartik\datetime\DateTimePicker::TYPE_INPUT,
    'options' => ['placeholder' => ''],
    'convertFormat' => true,
    'pluginOptions' => [
        'format' => 'dd.MM.yyyy hh:i',
        'autoclose' => true,
        'weekStart' => 1,
        'startDateTime' => '01.01.1930 00:00',
        'endDateTime' => '01.01.2030 00:00',
        'todayBtn' => 'linked',
        'todayHighlight' => true,
    ]
]);

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
            'writeCallback' => function ($session) {
                return [
                    'user_id' => Yii::$app->user->id,
                    'user_ip' => Yii::$app->request->userIP,
                    'run_at' => time()
                ];
            },
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
            'mutex' => \yii\mutex\PgsqlMutex::class, // Mutex used to sync queries
//            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
//            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,
            'as queueSchedule' => \artsoft\queue\JobSchedule::class,
        ],
        'formatter' => [
            'datetimeFormat' => 'php:d.m.Y H:i',
            'dateFormat' => 'php:d.m.Y',
            'timeFormat' => 'php:H:i',
            'defaultTimeZone' => 'Europe/Moscow',
            'sizeFormatBase' => 1000
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
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
