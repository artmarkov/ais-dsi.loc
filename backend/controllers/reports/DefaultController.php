<?php

namespace backend\controllers\reports;

use common\models\studyplan\SchoolWorkload;
use common\models\studyplan\StudyplanDistrib;
use common\models\studyplan\StudyplanHistory;
use common\models\studyplan\StudyplanStat;
use common\models\subject\SubjectType;
use common\models\teachers\TarifStatement;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersScheduleGenerator;
use common\models\teachers\TeachersTimesheet;
use common\models\user\UserCommon;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class DefaultController extends MainController
{
    public $freeAccessActions = ['studyplan-distrib','school-workload'];

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['date_in', 'is_avans', 'subject_type_id', 'activity_list', 'update_list_flag', 'progress_flag']);
        $model_date->addRule(['date_in', 'subject_type_id', 'activity_list'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y'])
            ->addRule(['is_avans'], 'integer')
            ->addRule(['update_list_flag', 'progress_flag'], 'boolean');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_timesheet_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $model_date->subject_type_id = $session->get('_timesheet_subject_type_id') ?? SubjectType::find()->scalar();
            $model_date->activity_list = $model_date->subject_type_id == 1000 ? Yii::$app->user->getSetting('_timesheet_activity_list_0') : Yii::$app->user->getSetting('_timesheet_activity_list_1');
        }
        $session->set('_timesheet_date_in', $model_date->date_in);
        $session->set('_timesheet_subject_type_id', $model_date->subject_type_id);

        if ($model_date->update_list_flag) {
            $model_date->subject_type_id == 1000 ? Yii::$app->user->setSetting('_timesheet_activity_list_0', $model_date->activity_list) : Yii::$app->user->setSetting('_timesheet_activity_list_1', $model_date->activity_list);
        }
        // echo '<pre>' . print_r($model_date->subject_type_id, true) . '</pre>'; die();

        if (Yii::$app->request->post('submitAction') == 'excel') {
            $timesheet = new TeachersTimesheet($model_date);
            $timesheet->makeXlsx();
        }
        return $this->renderIsAjax('index', [
            'model_date' => $model_date,
        ]);
    }

    public function actionTarifStatement()
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
        // echo '<pre>' . print_r($model_date->subject_type_id, true) . '</pre>'; die();

        if (Yii::$app->request->post('submitAction') == 'excel') {
            $model = new TarifStatement($model_date);
            $model->makeXlsx();
//            echo '<pre>' . print_r($model, true) . '</pre>'; die();
        }

        return $this->renderIsAjax('tarif-statement', [
            'model_date' => $model_date,
        ]);
    }

    public function actionActivityList()
    {
        $out = $sell = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $subject_type_id = $parents[0];
                $sell = $subject_type_id == 1000 ? (Yii::$app->user->getSetting('_timesheet_activity_list_0') ?? []) : (Yii::$app->user->getSetting('_timesheet_activity_list_1') ?? []);
                $data = (new Query())->from('teachers_activity_view')->select('teachers_activity_id as id, teachers_activity_memo as name')->andFilterWhere(['=', 'user_common_status', UserCommon::STATUS_ACTIVE])->orderBy('fullname')->all();
                return json_encode(['output' => $data, 'selected' => $sell]);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actionStudyplanStat()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        $model_date->addRule('options', 'safe');
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

    public function actionStudyplanDistrib()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
        }
        $session->set('__backendPlanYear', $model_date->plan_year);


        if (Yii::$app->request->post('submitAction') == 'excel') {
            $models = new StudyplanDistrib($model_date);
            $data = $models->makeXlsx();
        }

        return $this->renderIsAjax('studyplan-distrib', [
            'model_date' => $model_date,
        ]);
    }

    public function actionSchoolWorkload()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
        }
        $session->set('__backendPlanYear', $model_date->plan_year);


        if (Yii::$app->request->post('submitAction') == 'excel') {
            $models = new SchoolWorkload($model_date);
            $data = $models->makeXlsx();
        }

        return $this->renderIsAjax('studyplan-distrib', [
            'model_date' => $model_date,
        ]);
    }

    public function actionTeachersSchedule()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $this->view->params['breadcrumbs'][] = 'Расписание преподавателя';
        $model_date = $this->modelDate;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->teachers_id = isset($_GET['id']) ? $_GET['id'] : ($session->get('_teachersScheduleReports') ?? Teachers::find()->joinWith(['user'])->where(['status' => Teachers::STATUS_ACTIVE])->scalar());
        }
        $session->set('_teachersScheduleReports', $model_date->teachers_id);
//        echo '<pre>' . print_r($model_date->plan_year, true) . '</pre>';die();
        $modelTeachers = Teachers::findOne($model_date->teachers_id);
        $data = $modelTeachers->getTeachersScheduleQuery($model_date->plan_year);
        $models = ArrayHelper::index($data, null, ['week_day']);
        return $this->render('teachers-schedule', [
            'models' => $models,
            'model_date' => $model_date,
            'modelTeachers' => $modelTeachers,
        ]);
    }

    public function actionGeneratorSchedule()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['plan_year', 'teachers_list', 'subject_type_flag']);
        $model_date->addRule(['plan_year', 'teachers_list'], 'required')
            ->addRule(['plan_year'], 'integer')
            ->addRule(['subject_type_flag'], 'boolean')
            ->addRule(['teachers_list'], 'safe');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            $model_date->teachers_list = Yii::$app->user->getSetting('_generator_schedule_teachers_list') ?? [];
        }
        $session->set('__backendPlanYear', $model_date->plan_year);
        Yii::$app->user->setSetting('_generator_schedule_teachers_list', $model_date->teachers_list);
        if (Yii::$app->request->post('submitAction') == 'excel') {
            $models = new TeachersScheduleGenerator($model_date);
            $models->makeXlsx();
//            echo '<pre>' . print_r($model, true) . '</pre>'; die();
        }

        return $this->renderIsAjax('teachers-generator', [
            'model_date' => $model_date,
        ]);
    }

    public function actionStudentHistory()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        $model_date->addRule(['studyplan_id'], 'required', ['message' => 'Необходимо выбрать "Учебный план"']);
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $model_date->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
        }
        $session->set('__backendPlanYear', $model_date->plan_year);

        return $this->renderIsAjax('student-history', [
            'model_date' => $model_date,
        ]);
    }

    public function actionStudentHistoryExcel($id)
    {
        $models = new StudyplanHistory($id);
        $models->makeXlsx();
    }
}