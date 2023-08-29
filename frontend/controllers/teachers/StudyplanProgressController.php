<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use common\models\education\LessonProgressView;
use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;

/**
 * StudyplanProgressController
 */
class StudyplanProgressController extends MainController
{
    public function actionIndex()
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $session = Yii::$app->session;
        if($session->get('_progress_teachers_id') != $this->teachers_id) {
            $session->remove('_progress_subject_sect_studyplan_id');
        }
        $model_date = new DynamicModel(['date_in', 'subject_sect_studyplan_id']);
        $model_date->addRule(['date_in', 'subject_sect_studyplan_id'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y']);

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
            $timestamp_in = $timestamp[0];
            $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
            $model_date->subject_sect_studyplan_id = $session->get('_progress_subject_sect_studyplan_id') ?? LessonProgressView::getSecListForTeachersDefault($this->teachers_id, $plan_year);
        }
        $session->set('_progress_date_in', $model_date->date_in);
        $session->set('_progress_subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id);

        $session->set('_progress_teachers_id', $this->teachers_id);

        $model = LessonProgressView::getDataTeachers($model_date, $this->teachers_id);
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
        if (Yii::$app->request->post('submitAction') == 'excel') {
            // TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-progress', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

}