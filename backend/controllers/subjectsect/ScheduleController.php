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
                $teachers_load_id = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['teachers_load_id'];
                $model_load = TeachersLoad::findOne($teachers_load_id);
                $model->teachers_id = $model_load->teachers_id;
                $model->direction_id = $model_load->direction_id;
                $model->week_num = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['week_num'];
                $model->week_day = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['week_day'];
                $model->time_in = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['time_in'];
                $model->time_out = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['time_out'];
                $model->auditory_id = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['auditory_id'];
                $model->description = $_POST['SubjectSectSchedule'][$studyplan_subject_id][$schedule_id]['description'];
                $modelSubject = StudyplanSubject::findOne($studyplan_subject_id);
                if ($modelSubject->isIndividual()) {
                    $model->studyplan_subject_id = $studyplan_subject_id;
                    $model->subject_sect_studyplan_id = null;
                } else {
                    $model->studyplan_subject_id = null;
                    $model->subject_sect_studyplan_id = $modelSubject->getSubjectSectStudyplan()->id;
                }

                $model->save(false);
                $value = $model->id;
                return Json::encode(['output' => $value, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }

}