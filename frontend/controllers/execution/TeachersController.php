<?php

namespace frontend\controllers\execution;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use artsoft\widgets\Notice;
use common\models\education\LessonItems;
use common\models\education\LessonItemsProgressView;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\efficiency\search\TeachersEfficiencySearch;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidejob\Bonus;
use common\models\history\EfficiencyHistory;
use common\models\history\LessonItemsHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\history\TeachersLoadHistory;
use common\models\history\TeachersPlanHistory;
use common\models\info\Document;
use common\models\info\search\DocumentSearch;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\schedule\search\ConsultScheduleViewSearch;
use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\schedule\SubjectScheduleConfirm;
use common\models\service\UsersCard;
use common\models\studyplan\search\ThematicViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\schedule\SubjectScheduleView;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\subject\SubjectType;
use common\models\subjectsect\SubjectSect;
use common\models\teachers\PortfolioView;
use common\models\teachers\search\TeachersLoadViewSearch;
use common\models\teachers\search\TeachersPlanSearch;
use common\models\teachers\search\TeachersSearch;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadView;
use common\models\teachers\TeachersPlan;
use common\models\teachers\TeachersTimesheet;
use common\models\user\UserCommon;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 * $model_date
 */
class TeachersController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';
    public $modelHistoryClass = 'common\models\history\TeachersHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/teachers/default';

        parent::init();
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $model_date = $this->modelDate;
                if (!$model_date->teachers_id) {
                    $model_date->teachers_id = $id;
                } elseif ($model_date->teachers_id != $id) {
                    $id = $model_date->teachers_id;
//                print_r($action->id); die();
                    $this->redirect(['/teachers/' . $id . '/' . $action->id]);
                }
            }
        }
        return true;
    }

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $modelClass = $this->modelClass;
        $query = Teachers::find()
            ->where(['in', 'teachers.id', Teachers::getTeachersForTeacher($this->teachers_id)])
            ->andWhere(['!=', 'teachers.id', $this->teachers_id])
            ->andWhere(['=', 'status', UserCommon::STATUS_ACTIVE]);
        $searchModel = new TeachersSearch($query);
        $params = $this->getParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }


    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id, $readonly = true)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id);
        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_TEACHERS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;


        return $this->render('update', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
            'readonly' => $readonly
        ]);
    }

    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = Bonus::findOne(['id' => $id]);

        return json_encode(['id' => $model->bonus_vid_id, 'value' => $model->value_default]);
    }

    public function actionSetLoad()
    {
        $studyplan_subject_id = $_GET['studyplan_subject_id'];
        $teachers_load_id = isset($_GET['teachers_load_id']) ? $_GET['teachers_load_id'] : 0;
        $model = $teachers_load_id != 0 ? TeachersLoad::findOne($teachers_load_id) : new TeachersLoad();

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (isset($_POST['TeachersLoad'])) {
                $model->teachers_id = $_POST['TeachersLoad'][$studyplan_subject_id][$teachers_load_id]['teachers_id'];
                $model->week_time = $_POST['TeachersLoad'][$studyplan_subject_id][$teachers_load_id]['week_time'];
                $model->direction_id = $_POST['TeachersLoad'][$studyplan_subject_id][$teachers_load_id]['direction_id'];
                $modelSubject = StudyplanSubject::findOne($studyplan_subject_id);
                if ($modelSubject->isIndividual()) {
                    $model->studyplan_subject_id = $studyplan_subject_id;
                    $model->subject_sect_studyplan_id = 0;
                } else {
                    $model->studyplan_subject_id = 0;
                    $model->subject_sect_studyplan_id = $modelSubject->getSubjectSectStudyplan()->id;
                }

                $model->save(false);
                $value = $model->teachers_id;
                return Json::encode(['output' => $value, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }

    public function actionDirection()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = TeachersActivity::getDirectionListById($cat_id);
                $sel = count($out) == 1 ? $out[0]['id'] : '';
                return json_encode(['output' => $out, 'selected' => $sel]);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actionTeachers()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = Teachers::getTeachersById($cat_id);
                $sel = count($out) == 1 ? $out[0]['id'] : '';
                return json_encode(['output' => $out, 'selected' => $sel]);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actionDirectionVid()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $cat_id2 = $parents[1];
                $out = TeachersActivity::getDirectionVidListById($cat_id, $cat_id2);
                $sel = count($out) == 1 ? $out[0]['id'] : '';
                return json_encode(['output' => $out, 'selected' => $sel]);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actions()
    {
        $id = \Yii::$app->request->get('id');
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $widgets = [
            [
                [
                    'class' => 'col-md-3',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',
                    ],
                ],
                [
                    'class' => 'col-md-3',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
                [
                    'class' => 'col-md-3',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
                [
                    'class' => 'col-md-3',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
            ],
            [
                [
                    'class' => 'col-md-4',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',
                    ],
                ],
                [
                    'class' => 'col-md-4',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
                [
                    'class' => 'col-md-4',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
            ],
            [
                [
                    'class' => 'col-md-6',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',
                    ],
                ],

                [
                    'class' => 'col-md-6',
                    'content' => [
                        'common\widgets\EfficiencyUserBarWidget',

                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::actions(), [
            'monitor' => [
                'class' => 'backend\actions\MonitorTeachersAction',
                'widgets' => $widgets,
                'id' => $id,
            ]
        ]);
    }

    /**
     * @param $id
     * @param bool $readonly
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['extcution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }
        $model_date = $this->modelDate;

        return $this->render('schedule', [
            'model' => $model,
            'model_date' => $model_date,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param $id
     * @param null $objectId
     * @param null $mode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionLoadItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }
            $teachersLoadModel = StudyplanSubject::findOne(Yii::$app->request->get('studyplan_subject_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['execution/teachers/load-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление нагрузки';
            $model = new TeachersLoad();

            $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
            $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;
            $model->teachers_id = $id;

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-load/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['execution/teachers/load-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['execution/teachers/load-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = TeachersLoad::findOne($objectId);
            $data = new TeachersLoadHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersLoad::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['execution/teachers/load-items', 'id' => $model->id]];
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
            $query = TeachersLoadView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($id)])->andWhere(['=', 'plan_year', $model_date->plan_year]);
            $searchModel = new TeachersLoadViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('load-items', compact('dataProvider', 'searchModel', 'model_date', 'model'));
        }
    }

    public function actionThematicItems($id, $objectId = null, $mode = null, $readonly = true)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['execution/teachers/thematic-items', 'id' => $model->id]];
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
                        if (Yii::$app->request->post('submitAction') == 'approve') {
                            $model->doc_status = StudyplanThematic::DOC_STATUS_AGREED;
                            if ($model->approveMessage()) {
                                Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                            }
                        } elseif (Yii::$app->request->post('submitAction') == 'modif') {
                            $model->doc_status = StudyplanThematic::DOC_STATUS_MODIF;
                            if ($model->modifMessage()) {
                                Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                            }
                        }
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
            $model_date = $this->modelDate;

            $searchModel = new ThematicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['status'] = 1;
            $params[$searchName]['direction_id'] = 1000;
            $params[$searchName]['teachers_id'] = $id;
            $params[$searchName]['plan_year'] = $model_date->plan_year;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('thematic-items', compact('dataProvider', 'searchModel', 'model_date', 'model'));
        }
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model_date = $this->modelDate;

        $query = SubjectScheduleView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($id)])->andWhere(['=', 'plan_year', $model_date->plan_year]);
        $searchModel = new SubjectScheduleViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);
//print_r(Teachers::getTeachersByIds(User::getUsersByRole('signerSchedule')));

        $model_confirm = SubjectScheduleConfirm::find()->where(['=', 'teachers_id', $id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new SubjectScheduleConfirm();
        $model_confirm->teachers_id = $id;
        $model_confirm->plan_year = $model_date->plan_year;
        if (Yii::$app->request->post('submitAction') == 'send_approve') {
            $model_confirm->confirm_status = SubjectScheduleConfirm::DOC_STATUS_WAIT;
        } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
            $model_confirm->confirm_status = SubjectScheduleConfirm::DOC_STATUS_MODIF;
        }
        if ($model_confirm->load(Yii::$app->request->post()) AND $model_confirm->save()) {
            Yii::$app->session->setFlash('info', 'Статус успешно изменен.');
            if ($model_confirm->sendAdminMessage()) {
                Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
            }
            return $this->redirect($this->getRedirectPage('schedule-items'));
        }
        return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model_date', 'model', 'model_confirm'));

    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

       if ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['execution/teachers/consult-items', 'id' => $modelTeachers->id]];
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

            $query = ConsultScheduleView::find()->where(['=', 'teachers_id', $id])
                ->andWhere(['status' => 1])
                ->andWhere(['=', 'plan_year', $model_date->plan_year]);
            $searchModel = new ConsultScheduleViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            $model_confirm = ConsultScheduleConfirm::find()->where(['=', 'teachers_id', $id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new ConsultScheduleConfirm();
            $model_confirm->teachers_id = $id;
            $model_confirm->plan_year = $model_date->plan_year;
           if (Yii::$app->request->post('submitAction') == 'send_approve') {
               $model_confirm->confirm_status = ConsultScheduleConfirm::DOC_STATUS_WAIT;
           } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
               $model_confirm->confirm_status = ConsultScheduleConfirm::DOC_STATUS_MODIF;
           }
            if ($model_confirm->load(Yii::$app->request->post()) AND $model_confirm->save()) {
                Yii::$app->session->setFlash('info', 'Статус успешно изменен.');
                if ($model_confirm->sendAdminMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
                return $this->redirect($this->getRedirectPage('consult-items'));
            }
            return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'model_date', 'modelTeachers', 'model_confirm'));
        }
    }

    /**
     * Журнал успеваемости групповых занятий
     * @param $id
     * @param null $objectId
     * @param null $mode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['execution/teachers/studyplan-progress', 'id' => $modelTeachers->id]];

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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['execution/teachers/studyplan-progress', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['execution/teachers/studyplan-progress', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $this->view->params['tabMenu'] = $this->getMenu($id);

            $model = LessonItems::findOne($objectId);
            $data = new LessonItemsHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));
        } elseif ('delete' == $mode && $objectId) {
            $deletedIDs = LessonProgress::find()->where(['=', 'lesson_items_id', $objectId])->column();
            LessonProgress::deleteAll(['id' => $deletedIDs]);
            $model = LessonItems::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));
        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Group Progress'), 'url' => ['execution/teachers/studyplan-progress', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $model = LessonItems::findOne($objectId);
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
                                // LessonProgress::deleteAll(['id' => $deletedIDs]);
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
            if ($session->get('_progress_teachers_id') && $session->get('_progress_teachers_id') != $modelTeachers->id) {
                $session->remove('_progress_subject_sect_studyplan_id');
            }
            $model_date = new DynamicModel(['date_in', 'subject_sect_studyplan_id', 'teachers_id']);
            $model_date->addRule(['date_in'], 'required')
                ->addRule(['date_in'], 'date', ['format' => 'php:m.Y'])
                ->addRule('teachers_id', 'integer')
                ->addRule('subject_sect_studyplan_id', 'integer');

            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $mon = date('m');
                $year = date('Y');
                $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
                $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
                $timestamp_in = $timestamp[0];
                $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
                $model_date->subject_sect_studyplan_id = $session->get('_progress_subject_sect_studyplan_id') ?? LessonProgressView::getSecListForTeachersDefault($id, $plan_year);
                $model_date->teachers_id = $modelTeachers->id;
            }
            $session->set('_progress_date_in', $model_date->date_in);
            $session->set('_progress_subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id);

            $session->set('_progress_teachers_id', $modelTeachers->id);

            $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
            $timestamp_in = $timestamp[0];
            $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
            $model = LessonProgressView::getDataTeachers($model_date, $id, $plan_year);
//            if (Yii::$app->request->post('submitAction') == 'excel') {
//                // TeachersEfficiency::sendXlsx($data);
//            }

            return $this->renderIsAjax('studyplan-progress', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));
        }
    }

    /**
     * Журнал успеваемости индивидуальных занятий
     * @param $id
     * @param null $objectId
     * @param null $mode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionStudyplanProgressIndiv($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Indiv Progress'), 'url' => ['execution/teachers/studyplan-progress', 'id' => $modelTeachers->id]];

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
                $modelsItems = $model->getLessonProgressTeachersNew($id, $subject_key, $timestamp_in, $model);
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
                    } catch (Exception $e) {
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

        } elseif ('delete' == $mode && $objectId) {

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
            foreach ($models as $model) {
                $modelProgress = LessonProgress::findOne(['id' => $model->lesson_progress_id]);
                $modelProgress->delete();
                $modelLesson = LessonItems::findOne(['id' => $model->lesson_items_id]);
                $modelLesson->delete();
            }
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));
        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Indiv Progress'), 'url' => ['execution/teachers/studyplan-progress', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $subject_key = base64_decode($objectId);
            $keyArray = explode('||', $subject_key);
            $subject_key = $keyArray[0];
            $timestamp_in = $keyArray[1];

            $modelLesson = LessonItemsProgressView::find()
                ->where(['=', 'subject_key', $subject_key])
                ->andWhere(['=', 'lesson_date', $timestamp_in])
                ->one();
            $model = LessonItems::findOne($modelLesson->lesson_items_id);
            $modelsItems = $model->getLessonProgressTeachers($id, $subject_key, $timestamp_in);

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
                    (Exception $e) {
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

        } else {
            $session = Yii::$app->session;
            if ($session->get('_progress_teachers_id') != $id) {
                $session->remove('_progress_subject_key');
            }
            $model_date = new DynamicModel(['date_in', 'subject_key', 'teachers_id']);
            $model_date->addRule(['date_in'], 'required')
                ->addRule(['date_in'], 'date', ['format' => 'php:m.Y'])
                ->addRule('teachers_id', 'integer')
                ->addRule('subject_key', 'string');

            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $mon = date('m');
                $year = date('Y');

                $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
                $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
                $timestamp_in = $timestamp[0];
                $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
                $model_date->subject_key = $session->get('_progress_subject_key') ?? LessonProgressView::getIndivListForTeachersDefault($id, $plan_year);
                $model_date->teachers_id = $id;
//                print_r(LessonProgressView::getIndivListForTeachers($id, $plan_year)); die();
            }

            $session->set('_progress_date_in', $model_date->date_in);
            $session->set('_progress_subject_key', $model_date->subject_key);
            $session->set('_progress_teachers_id', $id);

            $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
            $timestamp_in = $timestamp[0];
            $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);
            $model = LessonProgressView::getDataIndivTeachers($model_date, $id, $plan_year);
            if (Yii::$app->request->post('submitAction') == 'excel') {
                // TeachersEfficiency::sendXlsx($data);
            }

            return $this->renderIsAjax('studyplan-progress-indiv', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));
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
    public function actionEfficiency($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['execution/teachers/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['execution/teachers/efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление записи';
            /* @var $model \artsoft\db\ActiveRecord */
            $model = new TeachersEfficiency();
            $id ? $model->teachers_id = [$id] : null;
            if ($model->load(Yii::$app->request->post())) {
                $valid = $model->validate();
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        foreach ($model->teachers_id as $id => $teachers_id) {
                            $m = new TeachersEfficiency();
                            $m->teachers_id = $teachers_id;
                            $m->efficiency_id = $model->efficiency_id;
                            $m->bonus_vid_id = $model->bonus_vid_id;
                            $m->bonus = $model->bonus;
                            $m->date_in = $model->date_in;
                            if (!($flag = $m->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                            $this->redirect($this->getRedirectPage('index'));
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
            return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
                'model' => $model,
                'modelDependence' => $modelTeachers,
                'readonly' => false
            ]);

        } elseif ('bar' == $mode) {

            $model_date = $this->modelDate;

            $data = TeachersEfficiency::getSummaryTeachersData($id, $model_date);

            return $this->renderIsAjax('efficiency-bar', compact(['data', 'model_date', 'modelTeachers']));

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['execution/teachers/efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['execution/teachers/efficiency', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $this->view->params['tabMenu'] = $this->getMenu($id);

            $model = TeachersEfficiency::findOne($objectId);
            $data = new EfficiencyHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));
        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersEfficiency::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));
        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['execution/teachers/efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = TeachersEfficiency::findOne($objectId);

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
                'model' => $model,
                'modelDependence' => $modelTeachers,
                'readonly' => false
            ]);
        } else {
            $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Efficiencies');
            $model_date = $this->modelDate;

            $data = ArtHelper::getStudyYearParams($model_date->plan_year);
            $query = TeachersEfficiency::find()->where(['=', 'teachers_id', $id])
                ->andWhere(['and', ['>=', 'date_in', $data['timestamp_in']], ['<=', 'date_in', $data['timestamp_out']]]);
            $searchModel = new TeachersEfficiencySearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('efficiency', compact(['dataProvider', 'searchModel', 'modelTeachers', 'model_date']));
        }
    }

    /**
     * @param $id
     * @param null $objectId
     * @param null $mode
     * @param bool $readonly
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDocument($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/execution/teachers/document', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление документа';
            $modelDoc = new Document();
            $modelDoc->user_common_id = $model->user_common_id;

            if ($modelDoc->load(Yii::$app->request->post()) && $modelDoc->save()) {
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelDoc);
            }
            return $this->renderIsAjax('/info/document/_form', [
                'model' => $modelDoc,
                'readonly' => $readonly,
                'teachers_id' => $id
            ]);


        } elseif ('delete' == $mode && $objectId) {
            $modelDoc = Document::findOne($objectId);
            $modelDoc->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelDoc));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/execution/teachers/document', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelDoc = Document::findOne($objectId);

            if (!isset($modelDoc)) {
                throw new NotFoundHttpException("The Document was not found.");
            }

            if ($modelDoc->load(Yii::$app->request->post()) && $modelDoc->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($modelDoc);
            }
            return $this->render('/info/document/_form', [
                'model' => $modelDoc,
                'readonly' => $readonly,
                'teachers_id' => $id
            ]);

        } else {
            $modelClass = 'common\models\info\Document';
            $searchModel = new DocumentSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $params[$searchName]['user_common_id'] = $model->user_common_id;

            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('document', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'modelTeachers' => $model
            ]);
        }
    }

    public function actionCheetAccount($id)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];

        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in', 'subject_type_id', 'activity_list', 'teachers_id']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y'])
            ->addRule('teachers_id', 'integer');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_timesheet_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $model_date->teachers_id = $id;
        }
        $session->set('_timesheet_date_in', $model_date->date_in);

        $model_date->activity_list = TeachersActivity::find()->where(['=', 'teachers_id', $id])->column();
        $model_date->subject_type_id = SubjectType::find()->column();

//        echo '<pre>' . print_r($model_date->activity_list, true) . '</pre>'; die();
        $model = new TeachersTimesheet($model_date);
        $model = $model->getTeachersCheetData();
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp[0]);

        return $this->renderIsAjax('cheet-account', compact(['model', 'model_date', 'modelTeachers', 'plan_year']));

    }

    public function actionPortfolio($id)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['execution/teachers/view', 'id' => $id]];

        $model_date = $this->modelDate;
        $data = ArtHelper::getStudyYearParams($model_date->plan_year);
        $dataProvider = new ActiveDataProvider(['query' => PortfolioView::find()->where(['teachers_id' => $id])->andWhere(['between', 'datetime_in', $data['timestamp_in'], $data['timestamp_out']])]);
        return $this->renderIsAjax('portfolio', compact(['dataProvider', 'model_date', 'id']));
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
//            ['label' => 'Монитор', 'url' => ['/execution/teachers/monitor', 'id' => $id]],
            ['label' => 'Карточка', 'url' => ['/execution/teachers/update', 'id' => $id]],
            ['label' => 'Нагрузка', 'url' => ['/execution/teachers/load-items', 'id' => $id]],
            ['label' => 'Табель учета', 'url' => ['/execution/teachers/cheet-account', 'id' => $id]],
            ['label' => 'Планирование инд. занятий', 'url' => ['/execution/teachers/teachers-plan', 'id' => $id]],
            ['label' => 'Злементы расписания', 'url' => ['/execution/teachers/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/execution/teachers/schedule', 'id' => $id]],
            ['label' => 'Тематические/репертуарные планы', 'url' => ['/execution/teachers/thematic-items', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/execution/teachers/consult-items', 'id' => $id]],
            ['label' => 'Журнал успеваемости группы', 'url' => ['/execution/teachers/studyplan-progress', 'id' => $id]],
            ['label' => 'Журнал успеваемости индивидуальных занятий', 'url' => ['/execution/teachers/studyplan-progress-indiv', 'id' => $id]],
            ['label' => 'Показатели эффективности', 'url' => ['/execution/teachers/efficiency', 'id' => $id]],
            ['label' => 'Портфолио', 'url' => ['/execution/teachers/portfolio', 'id' => $id]],
            ['label' => 'Документы', 'url' => ['/execution/teachers/document', 'id' => $id]],
        ];
    }
}
