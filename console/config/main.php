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

//            'migrationNamespaces' => [
//                'zhuravljov\yii\queue\monitor\migrations',
//                'artsoft\queue\migrations',
//            ],
        ],
//         'migration' => [
//            'class' => 'bizley\migration\controllers\MigrationController',
//        ],
//        'monitor' => [
//            'class' => \zhuravljov\yii\queue\monitor\console\GcController::class,
//            'silent' => false,
//        ],
    ],
    'components' => [
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
        'scriptUrl' => 'http://artsoft.loc',
    ],
    ],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'params' => $params,
];
