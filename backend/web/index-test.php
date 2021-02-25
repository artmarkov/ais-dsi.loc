<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (YII_ENV !== 'test') {
    die('You are not allowed to access this file.');
}

// Composer
require(__DIR__ . '/../../vendor/autoload.php');

// Environment
require(__DIR__ . '/../../common/env.php');

// Yii
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

// Bootstrap application

require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');


$config = require(__DIR__ . '/../../tests/codeception/config/backend/acceptance.php');

(new yii\web\Application($config))->run();
