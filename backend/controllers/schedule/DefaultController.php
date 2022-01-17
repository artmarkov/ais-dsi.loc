<?php

namespace backend\controllers\schedule;

use Yii;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\subjectsect\SubjectSectSchedule';
    public $modelSearchClass = 'common\models\subjectsect\search\SubjectScheduleViewSearch';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        if (!Yii::$app->request->get('load_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
        }
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->setTeachersLoadModelCopy(Yii::$app->request->get('load_id'));  // из нагрузки преподавателя
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }
}