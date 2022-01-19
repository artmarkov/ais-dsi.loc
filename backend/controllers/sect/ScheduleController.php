<?php

namespace backend\controllers\sect;

use artsoft\widgets\ActiveForm;
use common\models\subjectsect\SubjectSect;
use Yii;
use yii\web\Response;

class ScheduleController extends MainController
{
    public $modelClass = 'common\models\subjectsect\SubjectSchedule';
    public $modelSearchClass = null;

    public function actionUpdateSchedule($id = null)
    {
        if ($id === null) {
            $eventData = Yii::$app->request->post('eventData');
            $id = $eventData['id'];
            $subject_sect_id = $eventData['subject_sect_id'];

            if ($id == 0) {
                $model = new $this->modelClass();
                $model->week_day = $eventData['week_day'] + 1;
                $model->time_in = $eventData['time_in'];
                $model->time_out = $eventData['time_out'];
                $model->save(false);
            } else {
                $model = $this->modelClass::findOne($id);
            }
        } else {
            $subject_sect_id = Yii::$app->request->get('subject_sect_id');
            $model = $this->modelClass::findOne($id);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $model->setTeachersLoadModelCopy()) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect(['sect/default/schedule', 'id' => $subject_sect_id]);
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }
        return $this->renderAjax('schedule-modal', [
            'model' => $model,
            'modelSubjectSect' => SubjectSect::findOne($subject_sect_id),
            'subject_sect_id' => $subject_sect_id,
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