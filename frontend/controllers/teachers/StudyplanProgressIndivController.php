<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use common\models\education\LessonProgressView;
use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;

/**
 * StudyplanProgressIndivController
 */
class StudyplanProgressIndivController extends MainController
{
    public function actionIndex()
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $session = Yii::$app->session;
        if($session->get('_progress_teachers_id') != $this->teachers_id) {
            $session->remove('_progress_subject_key');
        }
        $model_date = new DynamicModel(['date_in', 'subject_key']);
        $model_date->addRule(['date_in', 'subject_key'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y']);

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
            $timestamp_in = $timestamp[0];
            $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
            $model_date->subject_key = $session->get('_progress_subject_key') ?? LessonProgressView::getIndivListForTeachersDefault($this->teachers_id, $plan_year);
//                print_r(LessonProgressView::getIndivListForTeachers($id, $plan_year)); die();
        }

        $session->set('_progress_date_in', $model_date->date_in);
        $session->set('_progress_subject_key', $model_date->subject_key);
        $session->set('_progress_teachers_id', $this->teachers_id);

        $model = LessonProgressView::getDataIndivTeachers($model_date, $this->teachers_id);
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
        if (Yii::$app->request->post('submitAction') == 'excel') {
            // TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-progress-indiv', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

}