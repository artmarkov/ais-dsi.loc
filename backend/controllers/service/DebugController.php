<?php

namespace backend\controllers\service;

use Yii;
use artsoft\controllers\admin\BaseController;

class DebugController extends BaseController
{
    const DEBUG_COOKIE = 'yii_debug';

    public function actionIndex()
    {
        if (Yii::$app->request->cookies->has(self::DEBUG_COOKIE)) {
            Yii::$app->response->cookies->remove(self::DEBUG_COOKIE);
        } else {
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => self::DEBUG_COOKIE,
                'value' => true,
            ]));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}