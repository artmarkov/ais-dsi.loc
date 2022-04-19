<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),  
    require(__DIR__ . '/params.php')
);

$config =  [
    'id' => 'frontend',
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'auth' => [
            'class' => 'artsoft\auth\AuthModule',
        ],
        'fileinput' => [
            'class' => 'artsoft\fileinput\FileInputModule',
        ],
        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'class' => 'frontend\components\Theme',
                'theme' => env('FRONTEND_THEME'),
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
            'baseUrl' => '',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    // 'sourcePath' => '@artsoft/assets/theme/bootswatch/custom',
                    'sourcePath' => '@artsoft/assets/theme/bootstrap/dist/css',
                    'css' => ['bootstrap.css']
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'artsoft\web\MultilingualUrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '<module:auth>/<action:(logout|captcha|login)>' => '<module>/default/<action>',
                '<module:auth>/<action:(oauth)>/<authclient:\w+>' => '<module>/default/<action>',
                '/' => 'site/index',
                '<action:[\w \-]+>' => 'site/<action>',
                '<module:[\w_\-]+>/' => '<module>/default/index',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<mode:(create)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<objectId:\d+>/<mode:(view)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<objectId:\d+>/<mode:(update)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<objectId:\d+>/<mode:(delete)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<objectId:\d+>/<mode:(history)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>/<objectId:\d+>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<action:(create)>' => '<module>/default/<action>',
                '<module:[\w_\-]+>/<controller:[\w_\-]+>' => '<module>/<controller>/index',
                '<module:[\w_\-]+>/<controller:[\w_\-]+>/<id:\d+>/<action:[\w_\-]+>' => '<module>/<controller>/<action>',
                '<module:[\w_\-]+>/<controller:[\w_\-]+>/<action:[\w_\-]+>' => '<module>/<controller>/<action>',
            ],
//            'multilingualRules' => [
//                '<module:auth>/<action:\w+>' => '<module>/default/<action>',
//                '<controller:(category|tag)>/<slug:[\w \-]+>' => '<controller>/index',
//                '<controller:(category|tag)>' => '<controller>/index',
////                '<slug:[\w \-]+>' => 'site/index/',
//                '/' => 'site/index',
//                '<action:[\w \-]+>' => 'site/<action>',
//                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
//            ],
//            'nonMultilingualUrls' => [
//                'auth/default/oauth',
//            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authClientCollection' => require __DIR__ . '/_auth.php',
    ],
    'params' => $params,
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'artsoft\DebugModule',
        //'allowedIPs' => ['*', '127.0.0.1', '::1']
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
