<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use backend\models\Model;
use common\models\education\LessonItems;
use common\models\education\LessonItemsProgressView;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\education\LessonTest;
use common\models\education\ProgressConfirmIndiv;
use common\models\studyplan\Studyplan;
use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;
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
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'safe')
            ->addRule('subject_key', 'string')->addRule('date_in', function ($attribute)
            {
                if(Yii::$app->formatter->asTimestamp('01.'.$this->date_in) > Yii::$app->formatter->asTimestamp('01.'.$this->date_out)) $this->addError($attribute, 'Дата начала периода должна быть меньше даты окончания.');
            })
            ->addRule('date_in', function ($attribute)
            {
                $plan_year_1 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_in));
                $plan_year_2 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_out));
                if($plan_year_1  != $plan_year_2 ) $this->addError($attribute, 'Задайте период в рамках одного учебного года.');
            });

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $model_date->date_out = $session->get('_progress_date_out') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, date("t"), $year), 'php:m.Y');
            $timestamp = ArtHelper::getMonYearParamsFromList($model_date->date_in);
            $timestamp_in = $timestamp[0];
            $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
            $model_date->subject_key = $session->get('_progress_subject_key') ?? LessonProgressView::getIndivListForTeachersDefault($this->teachers_id, $plan_year);
//                print_r(LessonProgressView::getIndivListForTeachers($id, $plan_year)); die();
        }

        $session->set('_progress_date_in', $model_date->date_in);
        $session->set('_progress_date_out', $model_date->date_out);
        $session->set('_progress_subject_key', $model_date->subject_key);
        $session->set('_progress_teachers_id', $this->teachers_id);

        $timestamp = ArtHelper::getMonYearParamsFromList($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
        $model = LessonProgressView::getDataIndivTeachers($model_date, $this->teachers_id, $plan_year);

        $model_confirm = ProgressConfirmIndiv::find()->where(['=', 'teachers_id', $modelTeachers->id])
                ->andWhere(['=', 'subject_key', $model_date->subject_key])
                ->andWhere(['=', 'timestamp_month', $timestamp_in])
                ->one() ?? new ProgressConfirmIndiv();
        if($model_confirm->isNewRecord) {
            $model_confirm->teachers_sign = ProgressConfirmIndiv::getLastSigner($modelTeachers->id);
        }
        $model_confirm->teachers_id = $modelTeachers->id;
        $model_confirm->timestamp_month = $timestamp_in;
        $model_confirm->subject_key = $model_date->subject_key;

        if ($model_confirm->load(Yii::$app->request->post()) && $model_confirm->validate()) {
            if (Yii::$app->request->post('submitAction') == 'send_approve') {
                $model_confirm->confirm_status = ProgressConfirmIndiv::DOC_STATUS_WAIT;
                if ($model_confirm->sendApproveMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
                $model_confirm->confirm_status = ProgressConfirmIndiv::DOC_STATUS_MODIF;
            }
            if ($model_confirm->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction();
            }
        }

        return $this->renderIsAjax('studyplan-progress-indiv', compact(['model', 'model_date', 'modelTeachers', 'plan_year', 'model_confirm']));

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
            // echo '<pre>' . print_r($model, true) . '</pre>'; die();
            //$lessonTest = LessonTest::findOne($model->lesson_test_id);
            $modelsItems = $model->getLessonProgressTeachersNew($this->teachers_id, $subject_key, $timestamp_in, $model);
            //if ($lessonTest->test_category == LessonTest::CURRENT_WORK) {
                if (empty($modelsItems)) {
                    Notice::registerDanger('Дата занятия не соответствует расписанию!');
                    $model->addError('lesson_date', 'Дата занятия не соответствует расписанию!');
                } else {
                    $modelsItems = LessonItems::checkLessonsIndiv($modelsItems, $model);
                    if (empty($modelsItems)) {
                        Notice::registerDanger('Занятие уже добавлено для выбранной даты и дисциплины!');
                        $model->addError('lesson_date', 'Занятие уже добавлено для выбранной даты и дисциплины!');
                    }
              //  }
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
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
                $this->getSubmitAction($model); // пропускаем ошибку дублирования в lesson_progress
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

        $modelLesson = $modelLesson = (new Query())->from('lesson_items_progress_studyplan_view')
            ->where(['teachers_id' => $id])
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'lesson_date', $timestamp_in])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->one();
        $model = LessonItems::findOne($modelLesson['lesson_items_id']);
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

        $models = (new Query())->from('lesson_items_progress_studyplan_view')
            ->where(['teachers_id' => $id])
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'lesson_date', $timestamp_in])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->all();
//        echo '<pre>' . print_r($models, true) . '</pre>'; die();
        foreach ($models as $model) {
            $modelLesson = LessonItems::findOne(['id' => $model['lesson_items_id']]);
            $modelLesson ? $modelLesson->delete() : null;
        }
        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));

    }
}