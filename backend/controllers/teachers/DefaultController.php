<?php

namespace backend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\efficiency\search\TeachersEfficiencySearch;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidejob\Bonus;
use common\models\history\EfficiencyHistory;
use common\models\history\LessonItemsHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\history\TeachersLoadHistory;
use common\models\info\Document;
use common\models\info\search\DocumentSearch;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\search\ConsultScheduleViewSearch;
use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\service\UsersCard;
use common\models\studyplan\StudyplanSubject;
use common\models\schedule\SubjectScheduleView;
use common\models\teachers\search\TeachersLoadViewSearch;
use common\models\teachers\search\TeachersPlanSearch;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadView;
use common\models\teachers\TeachersPlan;
use common\models\user\UserCommon;
use yii\base\DynamicModel;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 */
class DefaultController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';
    public $modelHistoryClass = 'common\models\history\TeachersHistory';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $user = new User();
        $userCommon = new UserCommon();
        $userCard = new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_NEW;
        $model = new $this->modelClass;
        $modelsActivity = [new TeachersActivity];

        if ($userCommon->load(Yii::$app->request->post()) && $userCard->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsActivity = Model::createMultiple(TeachersActivity::class);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());

            // validate all models
            $valid = $userCommon->validate();
            $valid = $userCard->validate() && $valid;
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user->username = $userCommon->generateUsername();
                    $user->email = $userCommon->email;
                    $user->generateAuthKey();

                    if (Yii::$app->art->emailConfirmationRequired) {
                        $user->status = User::STATUS_INACTIVE;
                        $user->generateConfirmationToken();
                    }
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'teacher']);
                        $userCommon->user_category = UserCommon::USER_CATEGORY_TEACHERS;
                        $userCommon->user_id = $user->id;
                        if ($flag = $userCommon->save(false)) {
                            $userCard->user_common_id = $userCommon->id;
                            if ($flag = $userCard->save(false)) {
                                $model->user_common_id = $userCommon->id;
                                if ($flag = $model->save(false)) {
                                    foreach ($modelsActivity as $modelActivity) {
                                        $modelActivity->teachers_id = $model->id;
                                        if (!($flag = $modelActivity->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                    }
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
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
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
        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_TEACHERS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;

        if ($userCommon->load(Yii::$app->request->post()) && $userCard->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsActivity, 'id', 'id');
            $modelsActivity = Model::createMultiple(TeachersActivity::class, $modelsActivity);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsActivity, 'id', 'id')));

            // validate all models
            $valid = $userCommon->validate();
            // $valid = $userCard->validate() && $valid;
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
                        $userCard->user_common_id = $userCommon->id;
                        if ($flag && $flag = $userCard->save(false)) {
                            if ($flag = $model->save(false)) {
                                if (!empty($deletedIDs)) {
                                    TeachersActivity::deleteAll(['id' => $deletedIDs]);
                                }
                                foreach ($modelsActivity as $modelActivity) {
                                    $modelActivity->teachers_id = $model->id;
                                    if (!($flag = $modelActivity->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
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

        return $this->render('update', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = Bonus::findOne(['id' => $id]);

        return $model->value_default;
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

    public function actionTeachers()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = Teachers::getTeachersById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
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
    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        // $modelsSubject = $model->studyplanSubject;

        return $this->render('schedule', [
            'model' => $model,
            // 'modelsSubject' => (empty($modelsSubject)) ? [new StudyplanSubject()] : $modelsSubject,
            'readonly' => $readonly
        ]);
    }
    public function actionLoadItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }
            $teachersLoadModel = StudyplanSubject::findOne(Yii::$app->request->get('studyplan_subject_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['teachers/default/load-items', 'id' => $model->id]];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['teachers/default/load-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['teachers/default/load-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = TeachersLoad::findOne($objectId);
            $data = new TeachersLoadHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersLoad::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Load'), 'url' => ['teachers/default/load-items', 'id' => $model->id]];
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
            $query = TeachersLoadView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($id)]);
            $searchModel = new TeachersLoadViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('load-items', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            if (!Yii::$app->request->get('load_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
            }
            $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/default/schedule-items', 'id' => $model->id]];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/default/schedule-items', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['teachers/default/schedule-items', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SubjectSchedule::findOne($objectId);
            $data = new SubjectScheduleHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = SubjectSchedule::findOne($objectId);
            $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
            if (!isset($model)) {
                throw new NotFoundHttpException("The SubjectSchedule was not found.");
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
            $query = SubjectScheduleView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($id)]);
            $searchModel = new SubjectScheduleViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionTeachersPlan($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['teachers/default/teachers-plan', 'id' => $modelTeachers->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление записи';
            $model = new TeachersPlan();
            $model->teachers_id = $modelTeachers->id;
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-plan/_form.php', [
                'model' => $model,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = TeachersPlan::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['teachers/default/teachers-plan', 'id' => $modelTeachers->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['teachers/default/update', 'id' => $modelTeachers->id]];
            $data = new TeachersPlanHistory($objectId);
            return $this->renderIsAjax('/sect/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = TeachersPlan::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['teachers/default/teachers-plan', 'id' => $modelTeachers->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = TeachersPlan::findOne($objectId);

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/teachers/teachers-plan/_form.php', [
                'model' => $model,
            ]);

        } else {
            $searchModel = new TeachersPlanSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['teachers_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('teachers-plan', compact('dataProvider', 'searchModel', 'id'));
        }

    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['teachers/default/consult-items', 'id' => $modelTeachers->id]];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['teachers/default/consult-items', 'id' => $modelTeachers->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['teachers/default/update', 'id' => $modelTeachers->id]];
            $data = new ConsultScheduleHistory($objectId);
            return $this->renderIsAjax('/sect/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = ConsultSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['teachers/default/consult-items', 'id' => $modelTeachers->id]];
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
            $params[$searchName]['teachers_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Journal Progress'), 'url' => ['teachers/default/studyplan-progress', 'id' => $model->id]];

            if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
            }

            $subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;
            $studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;

            $model = new LessonItems();
            $model->studyplan_subject_id = $studyplan_subject_id;
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Journal Progress'), 'url' => ['teachers/default/studyplan-progress', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['teachers/default/studyplan-progress', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Journal Progress'), 'url' => ['teachers/default/studyplan-progress', 'id' => $model->id]];
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

            $model = LessonProgressView::getDataTeachers($model_date, $id);

            if (Yii::$app->request->post('submitAction') == 'excel') {
                // TeachersEfficiency::sendXlsx($data);
            }

            return $this->renderIsAjax('studyplan-progress', compact(['model', 'model_date']));
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
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['teachers/default/efficiency', 'id' => $id]];
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
                'class' => StringHelper::basename(TeachersEfficiency::className()),
                'readonly' => false
            ]);

        } elseif ('bar' == $mode) {
            $session = Yii::$app->session;

            $model_date = new DynamicModel(['plan_year']);
            $model_date->addRule(['plan_year'], 'required')
                ->addRule(['plan_year'], 'string');
            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $model_date->plan_year = $session->get('_efficiency_plan_year') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            }
            $session->set('_efficiency_plan_year', $model_date->plan_year);

            $data = TeachersEfficiency::getSummaryTeachersData($id, $model_date);

            return $this->renderIsAjax('efficiency-bar', compact(['data', 'model_date']));

        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['teachers/default/efficiency', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['teachers/default/efficiency', 'id' => $model->id, 'objectId' => $objectId, 'mode' => 'update']];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['teachers/default/efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = TeachersEfficiency::findOne($objectId);

            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
                'model' => $model,
                'class' => StringHelper::basename(TeachersEfficiency::className()),
                'readonly' => false
            ]);
        } else {
            $user = \common\models\teachers\Teachers::findOne($id)->getFullName();

            $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Efficiencies');
            $this->view->params['breadcrumbs'][] = $user;

            $searchModel = new TeachersEfficiencySearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['teachers_id'] = $id;
            $dataProvider = $searchModel->search($params);
            return $this->renderIsAjax('efficiency', compact(['dataProvider', 'searchModel', 'id']));
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
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/teachers/default/document', 'id' => $id]];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/teachers/default/document', 'id' => $id]];
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
                'teachers_id' => $id
            ]);
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Монитор', 'url' => ['/teachers/default/monitor', 'id' => $id]],
            ['label' => 'Карточка', 'url' => ['/teachers/default/update', 'id' => $id]],
            ['label' => 'Нагрузка', 'url' => ['/teachers/default/load-items', 'id' => $id]],
            ['label' => 'Планирование инд. занятий', 'url' => ['/teachers/default/teachers-plan', 'id' => $id]],
            ['label' => 'Злементы расписания', 'url' => ['/teachers/default/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/teachers/default/schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/teachers/default/consult-items', 'id' => $id]],
            ['label' => 'Журнал успеваемости', 'url' => ['/teachers/default/studyplan-progress', 'id' => $id]],
            ['label' => 'Показатели эффективности', 'url' => ['/teachers/default/efficiency', 'id' => $id]],
            ['label' => 'Портфолио', 'url' => ['/teachers/default/portfolio', 'id' => $id]],
            ['label' => 'Документы', 'url' => ['/teachers/default/document', 'id' => $id]],
        ];
    }
}
