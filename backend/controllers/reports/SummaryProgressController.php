<?php

namespace backend\controllers\reports;

use common\models\education\SummaryProgress;
use common\models\studyplan\StudyplanStat;
use common\models\subject\SubjectType;
use common\models\teachers\TarifStatement;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersScheduleGenerator;
use common\models\teachers\TeachersTimesheet;
use common\models\user\UserCommon;
use common\models\venue\VenueSity;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SummaryProgressController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['plan_year', 'education_cat_id', 'programm_id', 'subject_type_id', 'course', 'subject_id', 'vid_sert']);
        $model_date->addRule(['plan_year', 'education_cat_id', 'programm_id', 'vid_sert'], 'required')
            ->addRule(['plan_year', 'education_cat_id', 'subject_type_id', 'course', 'subject_id', 'vid_sert'], 'integer')
            ->addRule(['programm_id'], 'safe');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            $model_date->programm_id = $session->get('_summary_progress_programm_id') ?? '' /*EducationProgramm::getProgrammScalar()*/;
            $model_date->education_cat_id = $session->get('_summary_progress_education_cat_id') ?? '';
        }

        $session->set('__backendPlanYear', $model_date->plan_year);
        $session->set('_summary_progress_programm_id', $model_date->programm_id);
        $session->set('_summary_progress_education_cat_id', $model_date->education_cat_id);
            $models = new SummaryProgress($model_date);
        $model = $models->getData();
//            echo '<pre>' . print_r($models, true) . '</pre>'; die();
        if (Yii::$app->request->post('submitAction') == 'excel') {
            $models->sendXlsx();
        }
        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
            'model' => $model,
        ]);
    }
}