<?php

namespace backend\controllers\reports;

use Yii;
use yii\base\DynamicModel;

class StatisticsController extends \backend\controllers\DefaultController
{

    public function actionIndex()
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
        //  $model = new SummaryProgress($model_date);
        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
            // 'model' => $model->getData(),
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
        //  $model = new SummaryProgress($model_date);
        return $this->renderIsAjax('studyplan-stat', [
            'model_date' => $model_date,
            // 'model' => $model->getData(),
        ]);
    }

    public $tabMenu = [
        ['label' => 'Статистика по плану работы', 'url' => ['/reports/statistics/index']],
        ['label' => 'Статистика по учебной работе', 'url' => ['/reports/statistics/studyplan-stat']],
    ];
}