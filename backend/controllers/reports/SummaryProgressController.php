<?php

namespace backend\controllers\reports;

use common\models\education\SummaryProgress;
use Yii;
use yii\base\DynamicModel;

class SummaryProgressController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['plan_year', 'education_cat_id', 'programm_id', 'subject_type_id', 'subject_form_id', 'course'/*, 'vid_sert'*/]);
        $model_date->addRule(['plan_year', 'education_cat_id', 'programm_id'/*, 'vid_sert'*/], 'required')
            ->addRule(['plan_year', 'education_cat_id'/*, 'vid_sert'*/], 'integer')
            ->addRule(['programm_id', 'subject_form_id', 'subject_type_id', 'course', 'finish_flag'], 'safe');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            $model_date->programm_id = $session->get('_summary_progress_programm_id') ?: ''/*EducationProgramm::getProgrammScalar()*/;
            $model_date->education_cat_id = $session->get('_summary_progress_education_cat_id') ?: '';
            $model_date->subject_form_id = $session->get('_summary_progress_subject_form_id') ?: '';
            $model_date->subject_type_id = $session->get('_summary_progress_subject_type_id') ?: '';
            $model_date->course = $session->get('_summary_progress_course') ?: '';
            $model_date->finish_flag = $session->get('_summary_progress_finish_flag') ?: false;
        }

        $session->set('__backendPlanYear', $model_date->plan_year);
        $session->set('_summary_progress_programm_id', $model_date->programm_id);
        $session->set('_summary_progress_education_cat_id', $model_date->education_cat_id);
        $session->set('_summary_progress_subject_form_id', $model_date->subject_form_id);
        $session->set('_summary_progress_subject_type_id', $model_date->subject_type_id);
        $session->set('_summary_progress_course', $model_date->course);
        $session->set('_summary_progress_finish_flag', $model_date->finish_flag);
        $model = new SummaryProgress($model_date);
        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
            'model' => $model->getData(),
        ]);
    }
}