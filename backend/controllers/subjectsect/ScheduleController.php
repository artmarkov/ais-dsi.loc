<?php

namespace backend\controllers\subjectsect;

use backend\models\Model;
use common\models\subjectsect\SubjectSectSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class ScheduleController extends MainController
{
    public $modelClass = 'common\models\subjectsect\SubjectSectSchedule';
    public $modelSearchClass = null;

    public function actionSetSchedule()
    {
        $studyplan_subject_id = $_GET['studyplan_subject_id'];
        $schedule_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : 0;
        $model = $schedule_id != 0 ? SubjectSectSchedule::findOne($schedule_id) : new SubjectSectSchedule();

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (isset($_POST['SubjectSectSchedule'])) {
                $postLoad = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id];

                $model->setModelAttributes($postLoad, $studyplan_subject_id);
                //$valid = $model->validate();
                // $valid = true;
                if (!$model->hasErrors()) {
                    $model->save(false);
                    $value = $model->id;
                    return ['output' => $value, 'message' => ''];
                } else {
                    return ['output' => '', 'message' => Json::encode($model->getErrors())];
                }
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }

    /**
     * Кликаем по событию расписания занятий ученика или в пустое поле
     * @return string|\yii\web\Response
     */
    public function actionInitSchedule()
    {
        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];
        $studyplan_id = $eventData['studyplan_id'];

        if ($id == 0) {
            $model = new $this->modelClass();
            $model->week_day = $eventData['week_day'] + 1;
            $model->time_in = $eventData['time_in'];
            $model->time_out = $eventData['time_out'];
        } else {
            $model = $this->modelClass::findOne($id);
        }
        return $this->renderAjax('@backend/views/studyplan/default/schedule-modal.php', [
            'model' => $model,
            'studyplan_id' => $studyplan_id
        ]);

    }

    public function actionUpdateSchedule($id)
    {
        $studyplan_id = Yii::$app->request->get('studyplan_id');
        $model = $this->modelClass::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
            return $this->redirect(['studyplan/default/studyplan-schedule', 'id' => $studyplan_id]);
        }
    }

    public function actionCreateSchedule()
    {
        $studyplan_id = Yii::$app->request->get('studyplan_id');
        $model = new $this->modelClass();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            return $this->redirect(['studyplan/default/studyplan-schedule', 'id' => $studyplan_id]);
        }
    }

    public function actionChangeSchedule()
    {
        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];
        $model = $this->modelClass::findOne($id);
        $model->week_day = $eventData['week_day'] + 1;
        $model->time_in = $eventData['time_in'];
        $model->time_out = $eventData['time_out'];
        if ($model->save()) {
            return true;
        }
        return false;
    }

    public function actionDeleteSchedule($id)
    {
        $studyplan_id = Yii::$app->request->get('studyplan_id');
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect(['studyplan/default/studyplan-schedule', 'id' => $studyplan_id]);
    }

}