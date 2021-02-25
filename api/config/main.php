<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'api',
    'homeUrl' => '/api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\common\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => \api\modules\v1\Module::class,
    ],
    'components' => [
        'request' => [
//            'cookieValidationKey' => env('API_COOKIE_VALIDATION_KEY'),
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
            ],
            'baseUrl' => '/api',
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'api\common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'class' => 'artsoft\components\User',
            'on afterLogin' => function ($event) {
                return;
            }
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'auth' => 'site/login',

                'GET profile' => 'profile/index',
                'PUT,PATCH profile' => 'profile/update',
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' =>  ['v1/post'],
//                    'prefix' => 'api', //api будет доступен по url, начинающимся с /api/
                ],
            ],
        ],
    ],
    'params' => $params,
];
