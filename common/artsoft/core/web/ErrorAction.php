<?php

namespace artsoft\web;

use artsoft\Art;
use Yii;

class ErrorAction extends \yii\web\ErrorAction
{
    public function run()
    {
//        print_r(Yii::$app->response->getStatusCode());
        if (Art::isFrontend()) {
            return Yii::$app->response->redirect(Yii::$app->homeUrl);
        }
        return parent::run();
    }
}
