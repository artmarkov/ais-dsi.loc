<?php

namespace backend\controllers\reports;

use common\models\reports\SchoolplanReport;
use common\models\reports\StudyplanProgressReport;
use common\models\reports\StudyplanReport;
use Yii;
use yii\base\DynamicModel;

class StatisticsController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;
        $day_in = 1;
        $day_out = date("t");
        $model_date = new DynamicModel(['date_in', 'date_out']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'safe');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_schoolplan_stat_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day_in, $year), 'php:m.Y');
            $model_date->date_out = $session->get('_schoolplan_stat_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:m.Y');
        }

        $session->set('_schoolplan_stat_date_in', $model_date->date_in);
        $session->set('_schoolplan_stat_date_out', $model_date->date_out);

        $model = new SchoolplanReport($model_date);
        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
            'model' => $model->getData(),
        ]);
    }

    public function actionStudyplanStat()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['plan_year']);
        $model_date->addRule(['plan_year'], 'required')
            ->addRule(['plan_year'], 'integer');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
        }

        $session->set('__backendPlanYear', $model_date->plan_year);
        $model = new StudyplanReport($model_date);
        return $this->renderIsAjax('studyplan-stat', [
            'model_date' => $model_date,
            'data' => $model->getData(),
        ]);
    }

    public function actionStudyplanProgressStat()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['plan_year', 'education_cat_id', 'programm_id', 'subject_type_id', 'subject_form_id', 'course']);
        $model_date->addRule(['plan_year', 'education_cat_id', 'programm_id'], 'required')
            ->addRule(['plan_year', 'education_cat_id'], 'integer')
            ->addRule(['programm_id', 'subject_type_id', 'course', 'finish_flag', 'speciality_flag', 'subject_form_id'], 'safe');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {

            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            $model_date->programm_id = $session->get('_summary_progress_programm_id') ?: ''/*EducationProgramm::getProgrammScalar()*/
            ;
            $model_date->education_cat_id = $session->get('_summary_progress_education_cat_id') ?: '';
        }

        $session->set('__backendPlanYear', $model_date->plan_year);
        $session->set('_summary_progress_programm_id', $model_date->programm_id);
        $session->set('_summary_progress_education_cat_id', $model_date->education_cat_id);

        $model = new StudyplanProgressReport($model_date);
        return $this->renderIsAjax('studyplan-progress-stat', [
            'model_date' => $model_date,
            'model' => $model->getData(),
        ]);
    }

    public function actionVisitsStat()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        return $this->renderIsAjax('visits-stat');
    }

    public $tabMenu = [
        ['label' => 'Статистика по плану работы', 'url' => ['/reports/statistics/index']],
        ['label' => 'Статистика по учебной работе', 'url' => ['/reports/statistics/studyplan-stat']],
        ['label' => 'Статистика по успеваемости', 'url' => ['/reports/statistics/studyplan-progress-stat']],
        ['label' => 'Статистика по посещаемости', 'url' => ['/reports/statistics/visits-stat']],
    ];
}