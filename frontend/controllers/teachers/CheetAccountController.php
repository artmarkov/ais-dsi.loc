<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use common\models\subject\SubjectType;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersTimesheet;
use Yii;
use yii\base\DynamicModel;

/**
 * CheetAccountController
 */
class CheetAccountController extends MainController
{
    public function actionIndex()
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in', 'date_out', 'subject_type_id', 'activity_list']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $m = date('m');
            $y = date('Y');
            $t = date('t');

            $model_date->date_in = $session->get('_timesheet_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $m, 1, $y), 'php:d.m.Y');
            $model_date->date_out = $session->get('_timesheet_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $m, $t, $y), 'php:d.m.Y');
        }
        $session->set('_timesheet_date_in', $model_date->date_in);
        $session->set('_timesheet_date_out', $model_date->date_out);

        $model_date->activity_list = TeachersActivity::find()->where(['=', 'teachers_id', $this->teachers_id])->column();
        $model_date->subject_type_id = SubjectType::find()->column();
//        echo '<pre>' . print_r($model_date->activity_list, true) . '</pre>'; die();
        $model = [];
        $model =  new TeachersTimesheet($model_date);
        $model = $model->getTeachersCheetData();
        // $model = LessonProgressView::getDataIndivTeachers($model_date, $id);
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);

        return $this->renderIsAjax('cheet-account', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

}