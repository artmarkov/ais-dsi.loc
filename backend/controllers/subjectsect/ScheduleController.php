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

}