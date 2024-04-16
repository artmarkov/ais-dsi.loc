<?php

namespace backend\controllers\studyplan;

use backend\models\Model;
use common\models\education\EducationProgrammLevel;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\history\ConsultScheduleHistory;
use common\models\history\LessonItemsHistory;
use common\models\history\SchoolplanPerformHistory;
use common\models\history\StudyplanHistory;
use common\models\history\StudyplanInvoicesHistory;
use common\models\history\SubjectCharacteristicHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\history\TeachersLoadHistory;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\search\ConsultScheduleStudyplanViewSearch;
use common\models\schoolplan\SchoolplanPerform;
use common\models\schoolplan\search\SchoolplanPerformSearch;
use common\models\students\Student;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\search\StudyplanSearch;
use common\models\studyplan\search\StudyplanThematicViewSearch;
use common\models\studyplan\search\SubjectCharacteristicViewSearch;
use common\models\studyplan\StudyplanInvoices;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\studyplan\SubjectCharacteristic;
use common\models\schedule\search\SubjectScheduleStudyplanViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\search\TeachersLoadStudyplanViewSearch;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadStudyplanView;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\studyplan\Studyplan';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanSearch';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $model_date = $this->modelDate;
                if (!$model_date->studyplan_id) {
                    $model_date->studyplan_id = $id;
                } elseif ($model_date->studyplan_id != $id) {
                    $id = $model_date->studyplan_id;
                    $this->redirect(['/studyplan/' . $id . '/' . $action->id]);
                }
            }
        }
        return true;
    }
    public function actionIndex()
    {
        $model_date = $this->modelDate;
        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }
        $teachers_id = null;
        if ($model_date->teachers_id != null) {
            $studyplanIDS = TeachersLoadStudyplanView::find()
                ->select('studyplan_id')
                ->distinct('studyplan_id')
                ->where(['=', 'teachers_id', $model_date->teachers_id])
                ->column();
            $query = Studyplan::find()
                ->where(['in', 'studyplan.id', $studyplanIDS])
                ->andWhere(['=', 'plan_year', $model_date->plan_year]);
        } else {
            $query = Studyplan::find()
                ->where(['=', 'plan_year', $model_date->plan_year]);
        }

        $searchModel = new StudyplanSearch($query);
        $params = $this->getParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel', 'model_date', 'teachers_id'));
    }

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');

        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {
            // validate all models
            $valid = $model->validate();
            // $valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $modelProgrammLevel = EducationProgrammLevel::find()
                        ->where(['programm_id' => $model->programm_id])
                        ->andWhere(['course' => $model->course])
                        ->one();
                    if ($modelProgrammLevel) {
                        $model->copyAttributes($modelProgrammLevel);
                    }
                    if ($flag = $model->save(false)) {

                        if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                            $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                            foreach ($modelsSubTime as $modelSubTime) {
                                $modelSub = new StudyplanSubject();
                                $modelSub->copyAttributes($model, $modelSubTime);

                                if (!($flag = $modelSub->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
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

        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsStudyplanSubject' => [new StudyplanSubject],
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        $modelsStudyplanSubject = $model->studyplanSubject;

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->post('submitAction') == 'next_class') {
                $model->status = 0;
                $model->status_reason = 1;
            } elseif (Yii::$app->request->post('submitAction') == 'repeat_class') {
                $model->status = 0;
                $model->status_reason = 2;
            } elseif (Yii::$app->request->post('submitAction') == 'finish_plan') {
                $model->status = 0;
                $model->status_reason = 3;
            } elseif (Yii::$app->request->post('submitAction') == 'restore') {
                $model->status = 1;
            }
            $oldIDs = ArrayHelper::map($modelsStudyplanSubject, 'id', 'id');
            $modelsStudyplanSubject = Model::createMultiple(StudyplanSubject::class, $modelsStudyplanSubject);
            Model::loadMultiple($modelsStudyplanSubject, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsStudyplanSubject, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsStudyplanSubject) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            StudyplanSubject::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsStudyplanSubject as $modelStudyplanSubject) {
                            $modelStudyplanSubject->studyplan_id = $model->id;
                            $modelStudyplanSubject->subject_type_id = $modelStudyplanSubject->subject_type_id != null ? $modelStudyplanSubject->subject_type_id : null;
                            if (!($flag = $modelStudyplanSubject->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction();
                    }
                } catch (Exception $e) {
                    print_r($e->errorInfo);
                    die();
                    $transaction->rollBack();
                }
            }
        }
        if (Yii::$app->request->post('submitAction') == 'doc_contract' || Yii::$app->request->post('submitAction') == 'doc_statement') {
            if (!isset($model->parent) || !$model->doc_date || !$model->doc_contract_start || !$model->doc_contract_end) {
                Yii::$app->session->setFlash('warning', 'Заполните поля раздела "Документы"');
                return $this->getSubmitAction($model);
            }
        }
        if (Yii::$app->request->post('submitAction') == 'doc_contract') {
            if ($model->subject_form_id != 1001) {
                $model->makeDocx(Studyplan::template_csf);
            } else {
                $model->makeDocx(Studyplan::template_cs);
            }
        } elseif (Yii::$app->request->post('submitAction') == 'doc_statement') {
            $model->makeDocx(Studyplan::template_ss);
        }
        return $this->render('update', [
            'model' => $model,
            'modelsStudyplanSubject' => (empty($modelsStudyplanSubject)) ? [new StudyplanSubject] : $modelsStudyplanSubject,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionHistory($id)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $data = new StudyplanHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }

    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }

        return $this->render('schedule', [
            'model' => $model,
            'model_date' => $model_date,
            'readonly' => $readonly
        ]);
    }

    public function actionLoadItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }
            $teachersLoadModel = StudyplanSubject::findOne(Yii::$app->request->get('studyplan_subject_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['studyplan/default/load-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление нагрузки';
            $model = new TeachersLoad();

            $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
            $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-load/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['studyplan/default/load-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/load-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = TeachersLoad::findOne($objectId);
            $data = new TeachersLoadHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersLoad::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['studyplan/default/load-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = TeachersLoad::findOne($objectId);
            $teachersLoadModel = StudyplanSubject::findOne($model->studyplan_subject_id);
            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanSubject was not found.");
            }

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-load/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);

        } else {

            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new TeachersLoadStudyplanViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('load-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new SubjectScheduleStudyplanViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new ConsultScheduleStudyplanViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionCharacteristicItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new SubjectCharacteristicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('characteristic-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionThematicItems($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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

                if (Yii::$app->request->post('submitAction') == 'approve') {
                    $model->doc_status = StudyplanThematic::DOC_STATUS_AGREED;
                } elseif (Yii::$app->request->post('submitAction') == 'send_admin_message') {
                    $model->doc_status = StudyplanThematic::DOC_STATUS_DRAFT;
                }
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
                        print_r($e->errorInfo);
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
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new StudyplanThematicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('thematic-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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
                        print_r($e->errorInfo);
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

//                $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
                $modelsItems = Model::createMultiple(LessonProgress::class);
                Model::loadMultiple($modelsItems, Yii::$app->request->post());
//                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItems, 'id', 'id')));

                // validate all models
                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsItems) && $valid;

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
//                            if (!empty($deletedIDs)) {
                                // LessonProgress::deleteAll(['id' => $deletedIDs]);
//                            }
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

            $model_date = $this->modelDate;
            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }

            $model_date->addRule(['date_in', 'date_out'], 'required')
                ->addRule(['date_in', 'date_out'], 'safe')
                ->addRule('date_in', function ($attribute)
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
            }
            $session->set('_progress_date_in', $model_date->date_in);
            $session->set('_progress_date_out', $model_date->date_out);

            $modelLessonProgress = LessonProgressView::getDataStudyplan($model_date, $id);

            if (Yii::$app->request->post('submitAction') == 'excel') {
                // TeachersEfficiency::sendXlsx($data);
            }

            return $this->renderIsAjax('studyplan-progress', [
                'model' => $modelLessonProgress,
                'model_date' => $model_date,
                'modelStudent' => $model
            ]);
        }
    }

    public function actionStudyplanPerform($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $plan_year = $model->plan_year;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
            $modelPerform = new SchoolplanPerform();
            $modelPerform->studyplan_id = $id;
            $modelPerform->status_sign = 0;

            if ($modelPerform->load(Yii::$app->request->post()) && $modelPerform->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelPerform);
            }

            return $this->renderIsAjax('@backend/views/schoolplan/perform/_form-studyplan.php', [
                'model' => $modelPerform,
                'plan_year' => $plan_year,
                'readonly' => false
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol'), 'url' => ['schoolplan/default/protocol-event', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['schoolplan/default/protocol-event', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SchoolplanPerform::findOne($objectId);
            $data = new SchoolplanPerformHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelPerform = SchoolplanPerform::findOne($objectId);
            $modelPerform->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelPerform));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Perform'), 'url' => ['schoolplan/default/perform', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelPerform = SchoolplanPerform::findOne($objectId);

            if ($modelPerform->load(Yii::$app->request->post()) && $modelPerform->validate()) {
                if (Yii::$app->request->post('submitAction') == 'approve') {
                    $modelPerform->status_sign = SchoolplanPerform::DOC_STATUS_AGREED;
                    if ($modelPerform->approveMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                } elseif (Yii::$app->request->post('submitAction') == 'modif') {
                    $modelPerform->status_sign = SchoolplanPerform::DOC_STATUS_MODIF;
                    if ($modelPerform->modifMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                }
                if ($modelPerform->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction();
                }
            }
            return $this->renderIsAjax('@backend/views/schoolplan/perform/_form-studyplan.php', [
                'model' => $modelPerform,
                'plan_year' => $plan_year,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Perform')];
            $searchModel = new SchoolplanPerformSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('perform-items', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'model' => $model]);
        }
    }

    public function actionStudyplanInvoices($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление карточки';

            $model =  new StudyplanInvoices();
            $model->status = StudyplanInvoices::STATUS_WORK;

            $studyplanIds = new DynamicModel(['ids']);
            $studyplanIds->addRule(['ids'], 'safe');
            $studyplanIds->ids = [$id];

            if ($model->load(Yii::$app->request->post()) && $studyplanIds->load(Yii::$app->request->post()) && $model->validate()) {
                $flag = true;
//            echo '<pre>' . print_r($model, true) . '</pre>'; die();
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach (explode(',', $model->invoices_reporting_month) as $i => $invoices_reporting_month) {
                        $m = new StudyplanInvoices();
                        $m->setAttributes($model->getAttributes());
                        $m->studyplan_id = $studyplanIds['ids'][0];
                        $m->invoices_reporting_month = $invoices_reporting_month;
                        if ($m->checkInvoicesExist()) {
                            if (!($flag = $m->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    print_r($e->errorInfo);
                    $transaction->rollBack();
                }
            }

            return $this->renderIsAjax('@backend/views/invoices/default/_form.php', [
                'model' => $model,
                'studyplanIds' => $studyplanIds,
                'readonly' => false,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = StudyplanInvoices::findOne($objectId);
            $data = new StudyplanInvoicesHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            $modelStudyplanInvoices->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelStudyplanInvoices));

        } elseif ('view' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $id]];

            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            return $this->renderIsAjax('@backend/views/invoices/default/view.php', [
                'model' => $modelStudyplanInvoices,
            ]);

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $model->id]];
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
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }

            $searchModel = new StudyplanInvoicesViewSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = ArrayHelper::merge(Yii::$app->request->getQueryParams(), [
                $searchName => [
                    'studyplan_id' => $id,
                    'plan_year' => $model_date->plan_year,
                ]
            ]);
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('invoices-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }


    /**
     * формируем список дисциплин для widget DepDrop::classname()
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSubject($id)
    {
        $model = $this->findModel($id);
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = $model->getSubjectById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionBulkNextClass()
    {
        if (Yii::$app->request->post('selection')) {
            $models = $this->modelClass::find()->where(['id' => Yii::$app->request->post('selection', [])])->all();
            $ret = false;
            foreach ($models as $model) {
                $model->status = $this->modelClass::STATUS_INACTIVE;
                $model->status_reason = 1;
                $ret = $model->update(false);
            }
            if ($ret) {
                Yii::$app->session->setFlash('success', 'Все выбранные учебные планы успешно обработаны.');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка пакетной обработки учебных планов');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionBulkRepeatClass()
    {
        if (Yii::$app->request->post('selection')) {
            $models = $this->modelClass::find()->where(['id' => Yii::$app->request->post('selection', [])])->all();
            $ret = false;
            foreach ($models as $model) {
                $model->status = $this->modelClass::STATUS_INACTIVE;
                $model->status_reason = 2;
                $ret = $model->update(false);
            }
            if ($ret) {
                Yii::$app->session->setFlash('success', 'Все выбранные учебные планы успешно обработаны.');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка пакетной обработки учебных планов');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionBulkFinishPlan()
    {
        if (Yii::$app->request->post('selection')) {
            $models = $this->modelClass::find()->where(['id' => Yii::$app->request->post('selection', [])])->all();
            $ret = false;
            foreach ($models as $model) {
                $model->status = $this->modelClass::STATUS_INACTIVE;
                $model->status_reason = 3;
                $ret = $model->update(false);
            }
            if ($ret) {
                Yii::$app->session->setFlash('success', 'Все выбранные учебные планы успешно обработаны.');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка пакетной обработки учебных планов');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionStudentsView($id)
    {
        $model = $this->findModel($id);
        $modelStudent = Student::findOne($model->student_id);
        $studentDependence = $modelStudent->studentDependence;
        $this->view->params['breadcrumbs'][] = ['label' => 'Карточка ученика'];
        $this->view->params['tabMenu'] = $this->getMenu($id);
        return $this->renderIsAjax('students_view', compact('modelStudent', 'studentDependence'));
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка ученика', 'url' => ['/studyplan/default/students-view', 'id' => $id]],
            ['label' => 'Карточка плана учащегося', 'url' => ['/studyplan/default/update', 'id' => $id]],
            ['label' => 'Нагрузка', 'url' => ['/studyplan/default/load-items', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/studyplan/default/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/studyplan/default/schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/studyplan/default/consult-items', 'id' => $id]],
            ['label' => 'Характеристики по предметам', 'url' => ['/studyplan/default/characteristic-items', 'id' => $id]],
            ['label' => 'Тематические/репертуарные планы', 'url' => ['/studyplan/default/thematic-items', 'id' => $id]],
            ['label' => 'Дневник успеваемости', 'url' => ['/studyplan/default/studyplan-progress', 'id' => $id]],
            ['label' => 'Оплата за обучение', 'url' => ['/studyplan/default/studyplan-invoices', 'id' => $id]],
            ['label' => 'Выполнение плана и участие в мероприятиях', 'url' => ['/studyplan/default/studyplan-perform', 'id' => $id]],
        ];
    }
}