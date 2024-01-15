<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use backend\models\Model;
use common\models\education\LessonItems;
use common\models\education\LessonItemsProgressView;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\studyplan\Studyplan;
use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;

/**
 * StudyplanProgressIndivController
 */
class StudyplanProgressIndivController extends MainController
{
    public function actionIndex()
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $session = Yii::$app->session;
        if ($session->get('_progress_teachers_id') != $this->teachers_id) {
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

        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
        $model = LessonProgressView::getDataIndivTeachers($model_date, $this->teachers_id, $plan_year);
        if (Yii::$app->request->post('submitAction') == 'excel') {
            // TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-progress-indiv', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

    public function actionCreate()
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Indiv Progress'), 'url' => ['teachers/studyplan-progress']];

        if (!Yii::$app->request->get('subject_key')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET subject_key");
        }

        $subject_key = base64_decode(Yii::$app->request->get('subject_key'));
        $keyArray = explode('||', $subject_key);
        $subject_key = $keyArray[0];
        $timestamp_in = $keyArray[1];

        $model = new LessonItems();
        $modelsItems = [];
        // предустановка учеников
        if (isset($_POST['submitAction']) && $_POST['submitAction'] == 'next') {
            $model->load(Yii::$app->request->post());
            $modelsItems = $model->getLessonProgressTeachersNew($this->teachers_id, $subject_key, $timestamp_in, $model);
            if (empty($modelsItems)) {
                Notice::registerDanger('Дата занятия не соответствует расписанию!');
            } else {
                $modelsItems = LessonItems::checkLessonsIndiv($modelsItems, $model->lesson_date);
                if (empty($modelsItems)) {
                    Notice::registerDanger('Занятие уже добавлено для выбранной даты и дисциплины!');
                }
            }
        } elseif ($model->load(Yii::$app->request->post())) {
            $modelsItems = Model::createMultiple(LessonProgress::class);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
            // $valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = true;
                    foreach ($modelsItems as $modelItems) {
                        $modelLesson = LessonItems::find()
                                ->where(['=', 'subject_sect_studyplan_id', 0])
                                ->andWhere(['=', 'studyplan_subject_id', $modelItems->studyplan_subject_id])
                                ->andWhere(['=', 'lesson_date', strtotime($model->lesson_date)])
                                ->one() ?? new LessonItems();
                        $modelLesson->studyplan_subject_id = $modelItems->studyplan_subject_id;
                        $modelLesson->lesson_date = $model->lesson_date;
                        $modelLesson->lesson_test_id = $model->lesson_test_id;
                        $modelLesson->lesson_topic = $model->lesson_topic;
                        $modelLesson->lesson_rem = $model->lesson_rem;
                        if (!($flag = $modelLesson->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                        $modelItems->lesson_items_id = $modelLesson->id;
                        if (!($flag = $modelItems->save(false))) {
                            $transaction->rollBack();
                            break;
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
        return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form-indiv.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'modelsItems' => $modelsItems,
            'subject_key' => $subject_key,
            'timestamp_in' => $timestamp_in,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->request->get('objectId')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET objectId");
        }
        $objectId = Yii::$app->request->get('objectId');
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $subject_key = base64_decode($objectId);
        $keyArray = explode('||', $subject_key);
        $subject_key = $keyArray[0];
        $timestamp_in = $keyArray[1];

        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Indiv Progress'), 'url' => ['teachers/studyplan-progress-indiv', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

        $modelLesson = LessonItemsProgressView::find()
            ->where(['=', 'subject_key', $subject_key])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $this->teachers_id]))
            ->andWhere(['=', 'lesson_date', $timestamp_in])
            ->andWhere(['=', 'plan_year', ArtHelper::getStudyYearDefault(null, $timestamp_in)])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->one();
        $model = LessonItems::findOne($modelLesson->lesson_items_id);
       // echo '<pre>' . print_r($model, true) . '</pre>';
        $modelsItems = $model->getLessonProgressTeachers($this->teachers_id, $subject_key, $timestamp_in);

        if ($model->load(Yii::$app->request->post())) {
            $modelsItems = Model::createMultiple(LessonProgress::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = true;
                    foreach ($modelsItems as $modelItems) {
                        $modelLesson = LessonItems::find()
                                ->where(['=', 'subject_sect_studyplan_id', 0])
                                ->andWhere(['=', 'studyplan_subject_id', $modelItems->studyplan_subject_id])
                                ->andWhere(['=', 'lesson_date', strtotime($model->lesson_date)])
                                ->one() ?? new LessonItems();
                        $modelLesson->studyplan_subject_id = $modelItems->studyplan_subject_id;
                        $modelLesson->lesson_date = $model->lesson_date;
                        $modelLesson->lesson_test_id = $model->lesson_test_id;
                        $modelLesson->lesson_topic = $model->lesson_topic;
                        $modelLesson->lesson_rem = $model->lesson_rem;
                        if (!($flag = $modelLesson->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                        $modelItems->lesson_items_id = $modelLesson->id;
                        if (!($flag = $modelItems->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch
                (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form-indiv.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'modelsItems' => $modelsItems,
            'subject_key' => $subject_key,
            'timestamp_in' => $timestamp_in,
        ]);
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->request->get('objectId')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET objectId");
        }
        $objectId = Yii::$app->request->get('objectId');
        $subject_key = base64_decode($objectId);
        $keyArray = explode('||', $subject_key);
        $subject_key = $keyArray[0];
        $timestamp_in = $keyArray[1];

        $models = LessonItemsProgressView::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $id]))
            ->where(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'lesson_date', $timestamp_in])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->all();
//        echo '<pre>' . print_r($models, true) . '</pre>'; die();
        foreach ($models as $model) {
            $modelLesson = LessonItems::findOne(['id' => $model->lesson_items_id]);
            $modelLesson->delete();
        }
        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));

    }
}