<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['gii', 'log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => 'console\controllers\MigrateController',
            'migrationPath' => '@app/migrations',
//
//            'migrationNamespaces' => [
//                'zhuravljov\yii\queue\monitor\migrations',
//                'artsoft\queue\migrations',
//            ],
        ],
        'migration' => [
            'class' => 'bizley\migration\controllers\MigrationController',
        ],
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\console\GcController::class,
            'silent' => false,
        ],
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'art*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@common/artsoft/messages',
                ],
            ],
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'scriptUrl' => 'https://stravinskiy.ru',
        ],
        'user' => [
            'class' => 'artsoft\helpers\ConsoleUser',
            'identityClass' => 'artsoft\models\User',
            'autoUserIdentityId' => env('CONSOLE_USER_ID')
        ],
    ],
    'modules' => [
        'gii' => 'yii\gii\Module',
        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
    ],
    'params' => $params,
];
