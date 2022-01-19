<?php

namespace backend\controllers\teachers;

use artsoft\models\User;
use common\models\guidejob\Bonus;
use common\models\subjectsect\search\SubjectScheduleViewSearch;
use common\models\subjectsect\SubjectSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\Subject;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersLoad;
use common\models\user\UserCommon;
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
        // $userCommon->scenario = UserCommon::SCENARIO_NEW;
        $model = new $this->modelClass;
        $modelsActivity = [new TeachersActivity];

        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsActivity = Model::createMultiple(TeachersActivity::class);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());

            // validate all models
            $valid = $userCommon->validate();
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
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;

        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsActivity, 'id', 'id');
            $modelsActivity = Model::createMultiple(TeachersActivity::class, $modelsActivity);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsActivity, 'id', 'id')));

            // validate all models
            $valid = $userCommon->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
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
            $model->setTeachersLoadModelCopy(Yii::$app->request->get('load_id'));  // из нагрузки преподавателя
            if ($model->load(Yii::$app->request->post()) AND $model->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }

            return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
                'model' => $model,
                'teachersLoadModel' => $teachersLoadModel,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = SubjectSchedule::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['teachers/default/update', 'id' => $model->id]];
            $data = new SubjectSectScheduleHistory($objectId);
            return $this->renderIsAjax('/teachers/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = SubjectSchedule::findOne($objectId);
            $teachersLoadModel = TeachersLoad::findOne($objectId);
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
            $params[$searchName]['teachers_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'id'));
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
            ['label' => 'Расписание занятий', 'url' => ['/teachers/default/schedule', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/teachers/default/schedule-items', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/teachers/default/consult', 'id' => $id]],
            ['label' => 'Табель учета', 'url' => ['/teachers/default/timesheet', 'id' => $id]],
            ['label' => 'Журнал успеваемости', 'url' => ['/teachers/default/progress', 'id' => $id]],
            ['label' => 'Показатели эффективности', 'url' => ['/teachers/default/efficiency', 'id' => $id]],
            ['label' => 'Портфолио', 'url' => ['/teachers/default/portfolio', 'id' => $id]],
        ];
    }
}
