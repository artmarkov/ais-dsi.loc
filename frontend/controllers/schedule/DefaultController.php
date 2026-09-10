<?php

namespace frontend\controllers\schedule;

use artsoft\helpers\RefBook;
use common\models\auditory\Auditory;
use common\models\schedule\ScheduleNetView;
use common\models\schedule\SubjectScheduleView;
use Yii;
use yii\helpers\ArrayHelper;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\schedule\SubjectScheduleView';
    public $modelSearchClass = 'common\models\schedule\search\SubjectScheduleViewSearch';


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
}