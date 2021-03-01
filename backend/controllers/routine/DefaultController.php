<?php

namespace backend\controllers\routine;

use common\models\calendar\Conference;

class DefaultController extends \backend\controllers\DefaultController
{
    public function actionIndex()
    {
        $this->view->title = 'Производственный календарь';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index.php');
    }

    public function actionInitEvent()
    {
        $model = new Conference();
        $model->start_date = \Yii::$app->request->post('startDate');
        $model->end_date = \Yii::$app->request->post('endDate');

        return $this->renderAjax('routine-modal', [
            'model' => $model
        ]);
    }
}