<?php

namespace backend\controllers\reports;

use common\models\studyplan\StudyplanStat;
use common\models\subject\SubjectType;
use common\models\teachers\TeachersTimesheet;
use Yii;
use yii\base\DynamicModel;

class DefaultController extends MainController
{

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['date_in', 'date_out', 'subject_type_id', 'activity_list']);
        $model_date->addRule(['date_in', 'date_out', 'subject_type_id', 'activity_list'], 'required')
            ->addRule(['date_in', 'date_out'], 'date');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $m = date('m');
            $y = date('Y');
            $t = date('t');

            $model_date->date_in = $session->get('_timesheet_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $m, 1, $y), 'php:d.m.Y');
            $model_date->date_out = $session->get('_timesheet_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $m, $t, $y), 'php:d.m.Y');
            $model_date->subject_type_id = $session->get('_timesheet_subject_type_id') ?? SubjectType::find()->scalar();
            $model_date->activity_list = Yii::$app->user->getSetting('_timesheet_activity_list') ?? [];
        }
        $session->set('_timesheet_date_in', $model_date->date_in);
        $session->set('_timesheet_date_out', $model_date->date_out);
        $session->set('_timesheet_subject_type_id', $model_date->subject_type_id);
        Yii::$app->user->setSetting('_timesheet_activity_list', $model_date->activity_list);
       // echo '<pre>' . print_r($model_date->subject_type_id, true) . '</pre>'; die();

        if (Yii::$app->request->post('submitAction') == 'excel') {
            $timesheet = new TeachersTimesheet($model_date);
            $timesheet->makeXlsx();
        }

        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
        ]);
    }

    public function actionStudyplanStat() {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
        }
        $session->set('__backendPlanYear', $model_date->plan_year);


        if (Yii::$app->request->post('submitAction') == 'excel') {
            $models = new StudyplanStat($model_date);
            $data = $models->getData();
            $models->sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-stat', [
            'model_date' => $model_date,
        ]);
    }
}