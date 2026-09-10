<?php

namespace backend\controllers\schedule;

use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\auditory\Auditory;
use common\models\schedule\ScheduleNetView;
use common\models\schedule\SubjectScheduleView;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubjectHist;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersPlan;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\schedule\SubjectScheduleView';
    public $modelSearchClass = 'common\models\schedule\search\SubjectScheduleViewSearch';
    public $freeAccessActions = ['select'];

    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $model_date = $this->modelDate;
        $model_date->addRule(['course','education_cat_id','programm_id'], 'safe');

        if(Yii::$app->request->post('submitAction') == 'send') {
            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {

            }
        }
       $model = ScheduleNetView::getData($model_date);
        $data = $model->getTeachersSchedule();
        $modelsAuditory = $model->getAuditoryModels();
        $studyplanSubjects = $model->getStudyplanSubjectAll();

      //  echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>'; die();
        return $this->renderIsAjax('index', compact('model_date', 'data', 'modelsAuditory', 'studyplanSubjects'));

    }

    /**
     * @return mixed
     */
    public function actionSelect()
    {
        $time_in = \Yii::$app->request->post('time_in');
        $time_duration = \Yii::$app->request->post('time_duration');
        $time_out = Schedule::decodeTime(Schedule::encodeTime($time_in) + $time_duration * 60);
        return json_encode(['time_out' => $time_out]);
    }

}