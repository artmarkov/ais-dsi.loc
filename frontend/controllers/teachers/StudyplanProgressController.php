<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use backend\models\Model;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

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

        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
        $model = LessonProgressView::getDataTeachers($model_date, $this->teachers_id, $plan_year);
        if (Yii::$app->request->post('submitAction') == 'excel') {
            // TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-progress', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['teachers/studyplan-progress']];

        if (!Yii::$app->request->get('subject_sect_studyplan_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET subject_sect_studyplan_id.");
        }

        $subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id');

        $model = new LessonItems();
        $model->scenario = LessonItems::SCENARIO_COMMON;
        $model->studyplan_subject_id = 0;
        $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
        // предустановка учеников
        $modelsItems = $model->getLessonProgressNew();

        if ($model->load(Yii::$app->request->post())) {
            $modelsItems = Model::createMultiple(LessonProgress::class);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItems as $modelItems) {
                            $modelItems->lesson_items_id = $model->id;
                            if (!($flag = $modelItems->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new LessonProgress] : $modelsItems,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['teachers/studyplan-progress']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);

        $model = LessonItems::findOne($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The LessonItems was not found.");
        }
        $model->scenario = LessonItems::SCENARIO_COMMON;
        $modelsItems = $model->getLessonProgress();
        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
            $modelsItems = Model::createMultiple(LessonProgress::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItems, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                          //  LessonProgress::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItems as $modelItems) {
                            $modelItems->lesson_items_id = $model->id;
                            if (!($flag = $modelItems->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $this->getSubmitAction($model);
                        }
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new LessonProgress] : $modelsItems,
        ]);

    }

    public function actionDelete($id)
    {
        $model = LessonItems::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));

    }
}