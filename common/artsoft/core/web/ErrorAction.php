<?php

namespace artsoft\web;

use artsoft\Art;
use backend\controllers\service\DebugController;
use Yii;

class ErrorAction extends \yii\web\ErrorAction
{
    public function run()
    {
      // print_r(Yii::$app->response->getStatusCode());
      /*  if (!Yii::$app->request->cookies->has(DebugController::DEBUG_COOKIE) && in_array(Yii::$app->response->getStatusCode(), [500])) {
            return Yii::$app->response->redirect(Yii::$app->homeUrl);
        }*/
        return parent::run();
    }
}
