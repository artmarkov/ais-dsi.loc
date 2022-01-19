<?php

namespace backend\controllers\schedule;

use common\models\teachers\TeachersLoad;
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
        $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->teachers_load_id = Yii::$app->request->get('load_id');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
            ]);
    }
}