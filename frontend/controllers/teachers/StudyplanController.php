<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use backend\models\Model;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\history\ConsultScheduleHistory;
use common\models\history\LessonItemsHistory;
use common\models\history\StudyplanInvoicesHistory;
use common\models\history\SubjectCharacteristicHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\search\ConsultScheduleStudyplanViewSearch;
use common\models\schedule\search\SubjectScheduleStudyplanViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\schoolplan\SchoolplanProtocol;
use common\models\schoolplan\search\SchoolplanProtocolViewSearch;
use common\models\students\Student;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\search\StudyplanSearch;
use common\models\studyplan\search\StudyplanThematicViewSearch;
use common\models\studyplan\search\SubjectCharacteristicViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanInvoices;
use common\models\studyplan\StudyplanSubject;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\studyplan\SubjectCharacteristic;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadStudyplanView;
use Yii;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * StudyplanController
 */
class StudyplanController extends MainController
{
    public $modelClass = 'common\models\studyplan\Studyplan';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanSearch';


    public function actionIndex()
    {
        $model_date = $this->modelDate;
        $teachers_id = $this->teachers_id;
        $model_date->teachers_id = $model_date->teachers_id ?? $teachers_id;
        $studyplanIDS = TeachersLoadStudyplanView::find()
            ->select('studyplan_id')
            ->distinct('studyplan_id')
            ->where(['=', 'teachers_id', $model_date->teachers_id])
            ->column();

        $query = Studyplan::find()
            ->where(['in', 'studyplan.id', $studyplanIDS])
            ->andWhere(['=', 'plan_year', $model_date->plan_year])
            ->andWhere(['=', 'studyplan.status', 1]);

        $searchModel = new StudyplanSearch($query);
        $params = $this->getParams();
        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax('@backend/views/studyplan/default/index.php', compact('dataProvider', 'searchModel', 'model_date', 'teachers_id'));

    }

    public function actionView($id)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => 'Карточка ученика'];
        $model = Studyplan::findOne($id);
        $modelStudent = Student::findOne($model->student_id);
        $studentDependence = $modelStudent->studentDependence;
        return $this->renderIsAjax('@backend/views/studyplan/default/students_view', compact('modelStudent', 'studentDependence'));
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            if (!Yii::$app->request->get('load_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
            }
            $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['studyplan/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление расписания';
            $model = new SubjectSchedule();
            $model->teachers_load_id = Yii::$app->request->get('load_id');
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['studyplan/default/schedule-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/schedule-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SubjectSchedule::findOne($objectId);
            $data = new SubjectScheduleHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['studyplan/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = SubjectSchedule::findOne($objectId);
            $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanSubject was not found.");
            }

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);

        } else {
            $searchModel = new SubjectScheduleStudyplanViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/schedule-items', compact('dataProvider', 'searchModel', 'model'));
        }
    }

    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        // $modelsSubject = $model->studyplanSubject;

        return $this->render('@backend/views/studyplan/default/schedule', [
            'model' => $model,
            // 'modelsSubject' => (empty($modelsSubject)) ? [new StudyplanSubject()] : $modelsSubject,
            'readonly' => $readonly
        ]);
    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['studyplan/default/consult-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление нагрузки';
            if (!Yii::$app->request->get('load_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
            }
            $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
            $model = new ConsultSchedule();
            $model->teachers_load_id = Yii::$app->request->get('load_id');
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schedule/consult-schedule/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['studyplan/default/consult-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/consult-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = ConsultSchedule::findOne($objectId);
            $data = new ConsultScheduleHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));


        } elseif ('delete' == $mode && $objectId) {
            $model = ConsultSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['studyplan/default/consult-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = ConsultSchedule::findOne($objectId);
            $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
            if (!isset($model)) {
                throw new NotFoundHttpException("The SubjectSchedule was not found.");
            }

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schedule/consult-schedule/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);

        } else {
            $searchModel = new ConsultScheduleStudyplanViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/consult-items', compact('dataProvider', 'searchModel', 'model'));
        }
    }

    public function actionCharacteristicItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Characteristic'), 'url' => ['studyplan/default/characteristic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление характеристики';
            if (!Yii::$app->request->get('studyplan_subject_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id.");
            }
            $studyplanSubjectModel = StudyplanSubject::findOne(Yii::$app->request->get('studyplan_subject_id'));
            $model = new SubjectCharacteristic();
            $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id');
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/studyplan/subject-characteristic/_form.php', [
                'model' => $model,
                'studyplanSubjectModel' => $studyplanSubjectModel,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $model = SubjectCharacteristic::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Characteristic'), 'url' => ['studyplan/default/characteristic-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/characteristic-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $data = new SubjectCharacteristicHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectCharacteristic::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Characteristic'), 'url' => ['studyplan/default/characteristic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = SubjectCharacteristic::findOne($objectId);
            $studyplanSubjectModel = StudyplanSubject::findOne($model->studyplan_subject_id);
            if (!isset($model)) {
                throw new NotFoundHttpException("The SubjectSchedule was not found.");
            }

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/studyplan/subject-characteristic/_form.php', [
                'model' => $model,
                'studyplanSubjectModel' => $studyplanSubjectModel,
            ]);

        } else {
            $searchModel = new SubjectCharacteristicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/characteristic-items', compact('dataProvider', 'searchModel', 'model'));
        }
    }

    public function actionThematicItems($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление плана';

            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }

            $model = new StudyplanThematic();
            $modelsItems = [new StudyplanThematicItems()];

            $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
            $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;

            if ($model->load(Yii::$app->request->post())) {
                $modelsItems = Model::createMultiple(StudyplanThematicItems::class);
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
                                $modelItems->studyplan_thematic_id = $model->id;
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
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
                'model' => $model,
                'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
                'readonly' => $readonly
            ]);

        } elseif ('history' == $mode && $objectId) {
            $model = StudyplanThematic::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['studyplan/default/update', 'id' => $model->id]];
            $data = new StudyplanThematicHistory($objectId);
            return $this->renderIsAjax('/studyplan/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = StudyplanThematic::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = StudyplanThematic::findOne($objectId);
            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanThematic was not found.");
            }
            $modelsItems = $model->studyplanThematicItems;

            if ($model->load(Yii::$app->request->post())) {

                $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
                $modelsItems = Model::createMultiple(StudyplanThematicItems::class, $modelsItems);
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
                                StudyplanThematicItems::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsItems as $modelItems) {

                                $modelItems->studyplan_thematic_id = $model->id;
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
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
                'model' => $model,
                'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
                'readonly' => $readonly
            ]);

        } else {
            $searchModel = new StudyplanThematicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/thematic-items', compact('dataProvider', 'searchModel', 'model'));
        }
    }

    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Studyplan Progress'), 'url' => ['studyplan/default/studyplan-progress', 'id' => $model->id]];

            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }

            $subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;
            $studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;

            $model = new LessonItems();
            $model->scenario = LessonItems::SCENARIO_COMMON;
            $model->studyplan_subject_id = $studyplan_subject_id;
            $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
            // предустановка учеников
            $modelsItems = $model->getLessonProgressNew();

            if ($model->load(Yii::$app->request->post())) {
//                if($model->checkLesson()){
//                    $this->redirect(Url::to(['/studyplan/default/studyplan-progress', 'id' => $id, 'objectId' => $model->checkLesson(), 'mode' => 'update']));
//
//                }
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
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
            return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form.php', [
                'model' => $model,
                'modelsItems' => (empty($modelsItems)) ? [new LessonProgress] : $modelsItems,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Studyplan Progress'), 'url' => ['studyplan/default/studyplan-progress', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/studyplan-progress', 'id' => $model->id, 'objectId' => $objectId, 'mode' => 'update']];
            $this->view->params['tabMenu'] = $this->getMenu($id);

            $model = LessonItems::findOne($objectId);
            $data = new LessonItemsHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = LessonItems::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));
        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Studyplan Progress'), 'url' => ['studyplan/default/studyplan-progress', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $model = LessonItems::findOne($objectId);
            if (!isset($model)) {
                throw new NotFoundHttpException("The LessonItems was not found.");
            }
            $model->scenario = LessonItems::SCENARIO_COMMON;
            $modelsItems = $model->getLessonProgress();
            if ($model->load(Yii::$app->request->post())) {

                $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
                $modelsItems = Model::createMultiple(LessonProgress::class);
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
                                LessonProgress::deleteAll(['id' => $deletedIDs]);
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
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/studyplan/lesson-items/_form.php', [
                'model' => $model,
                'modelsItems' => (empty($modelsItems)) ? [new LessonProgress] : $modelsItems,
//                'subject_sect_studyplan_id' => $subject_sect_studyplan_id,
//                'studyplan_subject_id' => $studyplan_subject_id,
            ]);

        } else {
            $session = Yii::$app->session;

            $model_date = new DynamicModel(['date_in']);
            $model_date->addRule(['date_in'], 'required')
                ->addRule(['date_in'], 'safe');

            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $mon = date('m');
                $year = date('Y');

                $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            }
            $session->set('_progress_date_in', $model_date->date_in);

            $modelLessonProgress = LessonProgressView::getDataStudyplan($model_date, $id, true);

            if (Yii::$app->request->post('submitAction') == 'excel') {
                // TeachersEfficiency::sendXlsx($data);
            }

            return $this->renderIsAjax('@backend/views/studyplan/default/studyplan-progress', [
                'model' => $modelLessonProgress,
                'model_date' => $model_date,
                'modelStudent' => $model
            ]);
        }
    }

    public function actionStudyplanPerform($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol Items'), 'url' => ['studyplan/default/studyplan-perform', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление карточки';
            $modelProtocolItems = new SchoolplanProtocol();
            if ($modelProtocolItems->load(Yii::$app->request->post()) AND $modelProtocolItems->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelProtocolItems);
            }

            return $this->renderIsAjax('@backend/views/schoolplan/schoolplan-protocol-items/_form.php', [
                'model' => $model,
                'modelProtocolItems' => $modelProtocolItems,
                'readonly' => false,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol Item'), 'url' => ['studyplan/default/studyplan-perform', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/studyplan-perform', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $modelProtocolItems = SchoolplanProtocol::findOne($objectId);
            $data = new ProtocolItemsHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['modelProtocolItems', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelProtocolItems = SchoolplanProtocol::findOne($objectId);
            $modelProtocolItems->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelProtocolItems));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol Items'), 'url' => ['studyplan/default/studyplan-perform', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelProtocolItems = SchoolplanProtocol::findOne($objectId);
            if (!isset($modelProtocolItems)) {
                throw new NotFoundHttpException("The SchoolplanProtocolItems was not found.");
            }

            if ($model->load(Yii::$app->request->post()) AND $modelProtocolItems->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schoolplan/schoolplan-protocol-items/_form.php', [
                'model' => $model,
                'modelProtocolItems' => $modelProtocolItems,
                'readonly' => false,
            ]);

        } else {
            $searchModel = new SchoolplanProtocolViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/protocol-items', compact('dataProvider', 'searchModel'));
        }
    }

    public function actionStudyplanInvoices($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['teachers/studyplan/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/studyplan/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['teachers/studyplan/default/studyplan-invoices', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление карточки';
            $modelStudyplanInvoices = new StudyplanInvoices();
            $modelStudyplanInvoices->studyplan_id = $id;
            if ($modelStudyplanInvoices->load(Yii::$app->request->post()) AND $modelStudyplanInvoices->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelStudyplanInvoices);
            }

            return $this->renderIsAjax('@backend/views/invoices/default/_form.php', [
//                'model' => $model,
                'model' => $modelStudyplanInvoices,
                'readonly' => false,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['teachers/studyplan/studyplan-invoices', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = StudyplanInvoices::findOne($objectId);
            $data = new StudyplanInvoicesHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            $modelStudyplanInvoices->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelStudyplanInvoices));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['teachers/studyplan/studyplan-invoices', 'id' => $model->id]];
                $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
                $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
                return $this->renderIsAjax('@backend/views/invoices/default/view.php', [
                    'model' => $modelStudyplanInvoices,
                ]);
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['teachers/studyplan/studyplan-invoices', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            if (!isset($modelStudyplanInvoices)) {
                throw new NotFoundHttpException("The StudyplanInvoices was not found.");
            }

            if ($modelStudyplanInvoices->load(Yii::$app->request->post()) AND $modelStudyplanInvoices->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($modelStudyplanInvoices);
            }

            return $this->renderIsAjax('@backend/views/invoices/default/_form.php', [
                // 'model' => $model,
                'model' => $modelStudyplanInvoices,
                'readonly' => false,
            ]);

        } else {
            $session = Yii::$app->session;

            $model_date = new DynamicModel(['studyplan_id','plan_year']);
            $model_date->addRule(['plan_year'], 'required')
                ->addRule(['plan_year'], 'string')
                ->addRule(['studyplan_id'], 'integer');
            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $model_date->plan_year = $session->get('_invoices_plan_year') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
                $model_date->studyplan_id = $id;
            }

            if ($model_date->studyplan_id != $id) {
                $this->redirect(['/teachers/studyplan/' . $model_date->studyplan_id . '/studyplan-invoices']);
            }

            $session->set('_invoices_studyplan_id', $model_date->studyplan_id);
            $session->set('_invoices_plan_year', $model->plan_year);

            $searchModel = new StudyplanInvoicesViewSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = ArrayHelper::merge($this->getParams(), [
                $searchName => [
                    'studyplan_id' => $model_date->studyplan_id,
                    'plan_year' => $model_date->plan_year,
                    'status' => Studyplan::STATUS_ACTIVE,
                ]
            ]);
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('@backend/views/studyplan/default/invoices-items', compact('dataProvider', 'searchModel', 'model_date', 'id'));
        }
    }

    public function actionMakeInvoices($id)
    {
        $model = StudyplanInvoices::findOne($id);
        return $model->makeDocx();
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка ученика', 'url' => ['/teachers/studyplan/view', 'id' => $id]],
//            ['label' => 'Нагрузка', 'url' => ['/teachers/studyplan/load-items', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/teachers/studyplan/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/teachers/studyplan/schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/teachers/studyplan/consult-items', 'id' => $id]],
            ['label' => 'Характеристики по предметам', 'url' => ['/teachers/studyplan/characteristic-items', 'id' => $id]],
            ['label' => 'Тематические/репертуарные планы', 'url' => ['/teachers/studyplan/thematic-items', 'id' => $id]],
            ['label' => 'Дневник успеваемости', 'url' => ['/teachers/studyplan/studyplan-progress', 'id' => $id]],
            ['label' => 'Оплата за обучение', 'url' => ['/teachers/studyplan/studyplan-invoices', 'id' => $id]],
//            ['label' => 'Выполнение плана и участие в мероприятиях', 'url' => ['/teachers/studyplan/studyplan-perform', 'id' => $id]],
        ];
    }
}