<?php

namespace backend\controllers\studyplan;

use artsoft\widgets\ActiveForm;
use Yii;
use yii\web\Response;

class ScheduleController extends MainController
{
    public $modelClass = 'common\models\schedule\SubjectSchedule';
    public $modelSearchClass = null;

    /**
     * Кликаем по событию расписания занятий ученика
     * @return string|\yii\web\Response
     */
    public function actionUpdateSchedule($id = null)
    {
        if ($id === null) {
            $eventData = Yii::$app->request->post('eventData');
            $id = $eventData['id'];
            $studyplan_id = $eventData['studyplan_id'];
            $model = $this->modelClass::findOne($id);
        } else {
            $studyplan_id = Yii::$app->request->get('studyplan_id');
            $model = $this->modelClass::findOne($id);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect(['studyplan/default/schedule', 'id' => $studyplan_id]);
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }
        return $this->renderAjax('schedule-modal', [
            'model' => $model,
            'studyplan_id' => $studyplan_id,
        ]);
    }

    public function actionChangeSchedule()
    {
        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];
        $model = $this->modelClass::findOne($id);
        $model->week_day = $eventData['week_day'] + 1;
        $model->time_in = $eventData['time_in'];
        $model->time_out = $eventData['time_out'];
        if ($model->save(false)) {
            return true;
        }
        return false;
    }

    public function actionDeleteSchedule()
    {
        $id = Yii::$app->request->post('id');
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->delete(false);

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return true;
    }

}