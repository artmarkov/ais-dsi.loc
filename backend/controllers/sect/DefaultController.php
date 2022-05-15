<?php

namespace backend\controllers\sect;

use backend\models\Model;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\history\LessonItemsHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\history\TeachersLoadHistory;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\search\ConsultScheduleStudyplanViewSearch;
use common\models\schedule\search\ConsultScheduleViewSearch;
use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\search\TeachersLoadViewSearch;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\subjectsect\SubjectSect';
    public $modelSearchClass = 'common\models\subjectsect\search\SubjectSectSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelsSubjectSectStudyplan = [new SubjectSectStudyplan()];

        if ($model->load(Yii::$app->request->post())) {

            $modelsSubjectSectStudyplan = Model::createMultiple(SubjectSectStudyplan::class);
            Model::loadMultiple($modelsSubjectSectStudyplan, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubjectSectStudyplan) && $valid;
            //$valid = true;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {

                            if ($flag === false) {
                                break;
                            }
                            $modelSubjectSectStudyplan->subject_sect_id = $model->id;
                            if (!($flag = $modelSubjectSectStudyplan->save(false))) {
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->createView, [
            'model' => $model,
            'modelsSubjectSectStudyplan' => (empty($modelsSubjectSectStudyplan)) ? [new SubjectSectStudyplan()] : $modelsSubjectSectStudyplan,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSect was not found.");
        }

        $modelsSubjectSectStudyplan = $model->subjectSectStudyplans;

        if ($model->load(Yii::$app->request->post())) {

            $oldSubjectIDs = ArrayHelper::map($modelsSubjectSectStudyplan, 'id', 'id');
            $modelsSubjectSectStudyplan = Model::createMultiple(SubjectSectStudyplan::class, $modelsSubjectSectStudyplan);
            Model::loadMultiple($modelsSubjectSectStudyplan, Yii::$app->request->post());
            $deletedSubjectIDs = array_diff($oldSubjectIDs, array_filter(ArrayHelper::map($modelsSubjectSectStudyplan, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubjectSectStudyplan) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedSubjectIDs)) {
                            SubjectSectStudyplan::deleteAll(['id' => $deletedSubjectIDs]);
                        }
                        foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {
                            $modelSubjectSectStudyplan->subject_sect_id = $model->id;
                            if (!($flag = $modelSubjectSectStudyplan->save(false))) {
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render($this->updateView, [
            'model' => $model,
            'modelsSubjectSectStudyplan' => (empty($modelsSubjectSectStudyplan)) ? [new SubjectSectStudyplan] : $modelsSubjectSectStudyplan,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param int $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionSchedule($id)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/view', 'id' => $model->id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->modelClass::findOne($id);
        $readonly = false;
        return $this->render('schedule', ['model' => $model,
            'readonly' => $readonly,
        ]);
    }

    public function actionLoadItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/view', 'id' => $model->id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            if (!Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET subject_sect_studyplan_id.");
            }
            $teachersLoadModel = StudyplanSubject::findOne(Yii::$app->request->get('subject_sect_studyplan_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['sect/default/load-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление нагрузки';
            $model = new TeachersLoad();
            $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id');
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-load/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['sect/default/load-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['sect/default/load-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = TeachersLoad::findOne($objectId);
            $data = new TeachersLoadHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersLoad::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['sect/default/load-items', 'id' => $model->id]];
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
            $searchModel = new TeachersLoadViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['subject_sect_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('load-items', compact('dataProvider', 'searchModel'));
        }
    }

    /**
     * @param $id
     * @param null $objectId
     * @param null $mode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/view', 'id' => $model->id]];

        if ('create' == $mode) {
            if (!Yii::$app->request->get('load_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
            }
            $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $model->id]];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['sect/default/schedule-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SubjectSchedule::findOne($objectId);
            $data = new SubjectScheduleHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $model->id]];
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
            $searchModel = new SubjectScheduleViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['subject_sect_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel'));
        }
    }

    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/view', 'id' => $model->id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['sect/default/studyplan-progress', 'id' => $model->id]];

            if (!Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET subject_sect_studyplan_id.");
            }
            $subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id');
            $model = new LessonItems();
            $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
            $model->studyplan_subject_id = 0;
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['sect/default/studyplan-progress', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['sect/default/studyplan-progress', 'id' => $model->id, 'objectId' => $objectId, 'mode' => 'update']];
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

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['sect/default/studyplan-progress', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $model = LessonItems::findOne($objectId);
            if (!isset($model)) {
                throw new NotFoundHttpException("The LessonItems was not found.");
            }
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
            ]);

        } else {
            $session = Yii::$app->session;

            $day_in = 1;
            $day_out = date("t");

            $model_date = new DynamicModel(['date_in', 'date_out', 'hidden_flag']);
            $model_date->addRule(['date_in', 'date_out'], 'required')
                ->addRule(['date_in', 'date_out'], 'date')
                ->addRule('hidden_flag', 'integer');

            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $mon = date('m');
                $year = date('Y');

                $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day_in, $year), 'php:d.m.Y');
                $model_date->date_out = $session->get('_progress_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
                $model_date->hidden_flag = $session->get('_progress_hidden_flag') ?? 0;
            }
            $session->set('_progress_date_in', $model_date->date_in);
            $session->set('_progress_date_out', $model_date->date_out);
            $session->set('_progress_hidden_flag', $model_date->hidden_flag);

            $model = LessonProgressView::getDataSect($model_date, $id);

            if (Yii::$app->request->post('submitAction') == 'excel') {
                // TeachersEfficiency::sendXlsx($data);
            }

            return $this->renderIsAjax('studyplan-progress', compact(['model', 'model_date']));
        }
    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['sect/default/view', 'id' => $id]];
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
            $model = ConsultSchedule::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['studyplan/default/consult-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['studyplan/default/update', 'id' => $model->id]];
            $data = new ConsultScheduleHistory($objectId);
            return $this->renderIsAjax('/studyplan/default/history', compact(['model', 'data']));

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
            $searchModel = new ConsultScheduleViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel'));
        }
    }
    /**
     * @return false|string
     */
    public function actionSubject()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $cat_id = $parents[1];
                $out = $this->modelClass::getSubjectForUnionAndCatToId($union_id, $cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * @return false|string
     */
    public function actionSubjectCat()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $out = $this->modelClass::getSubjectCategoryForUnionToId($union_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

}