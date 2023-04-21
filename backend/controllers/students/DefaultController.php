<?php

namespace backend\controllers\students;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\education\EducationProgrammLevel;
use common\models\entrant\Entrant;
use common\models\entrant\EntrantMembers;
use common\models\entrant\EntrantTest;
use common\models\entrant\search\EntrantSearch;
use common\models\history\EntrantHistory;
use common\models\history\StudyplanHistory;
use common\models\info\Document;
use common\models\info\search\DocumentSearch;
use common\models\service\UsersCard;
use common\models\students\StudentDependence;
use common\models\studyplan\search\StudyplanSearch;
use common\models\studyplan\search\StudyplanViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\SubjectType;
use common\models\user\UserCommon;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use Yii;


/**
 * DefaultController implements the CRUD actions for common\models\students\Student model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\students\Student';
    public $modelSearchClass = 'common\models\students\search\StudentSearch';
    public $modelHistoryClass = 'common\models\history\StudentsHistory';

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
        $modelsDependence = [new StudentDependence(['scenario' => StudentDependence::SCENARIO_PARENT])];

        if ($userCommon->load(Yii::$app->request->post()) && $userCard->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsDependence = Model::createMultiple(StudentDependence::class);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());

            // validate all models
            $valid = $userCommon->validate();
//            $valid = $userCard->validate() && $valid;
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsDependence) && $valid;
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
                        $user->assignRoles(['user', 'student']);
                        $userCommon->user_category = UserCommon::USER_CATEGORY_STUDENTS;
                        $userCommon->user_id = $user->id;
                        if ($flag = $userCommon->save(false)) {
                            $userCard->user_common_id = $userCommon->id;
                            if ($flag = $userCard->save(false)) {
                                $model->user_common_id = $userCommon->id;
                                if ($flag = $model->save(false)) {
                                    foreach ($modelsDependence as $modelDependence) {
                                        $modelDependence->student_id = $model->id;
                                        if (!($flag = $modelDependence->save(false))) {
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
                        $this->getSubmitAction();
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
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
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
        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_STUDENTS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->studentDependence;
        foreach ($modelsDependence as $m) {
            $m->scenario = StudentDependence::SCENARIO_PARENT;
        }
        if ($userCommon->load(Yii::$app->request->post()) && $userCard->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
            $modelsDependence = Model::createMultiple(StudentDependence::class, $modelsDependence);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependence, 'id', 'id')));

            // validate all models
            $valid = $userCommon->validate();
            // $valid = $userCard->validate() && $valid;
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsDependence) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
                        $userCard->user_common_id = $userCommon->id;
                        if ($flag && $flag = $userCard->save(false)) {
                            if ($flag = $model->save(false)) {
                                if (!empty($deletedIDs)) {
                                    StudentDependence::deleteAll(['id' => $deletedIDs]);
                                }
                                foreach ($modelsDependence as $modelDependence) {
                                    $modelDependence->student_id = $model->id;
                                    if (!($flag = $modelDependence->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction();
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
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionEntrant($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/student', 'Students'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['students/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление поступающего';

            $model = new Entrant();
            $model->student_id = $id;

            $modelsMembers = [new EntrantMembers];
            $modelsTest = [[new EntrantTest]];

            if ($model->load(Yii::$app->request->post())) {

                $modelsMembers = Model::createMultiple(EntrantMembers::class);
                Model::loadMultiple($modelsMembers, Yii::$app->request->post());

                // validate person and houses models
                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsMembers) && $valid;

                if (isset($_POST['EntrantTest'][0][0])) {
                    foreach ($_POST['EntrantTest'] as $index => $tests) {
                        foreach ($tests as $indexTest => $test) {
                            $data['EntrantTest'] = $test;
                            $modelTest = new EntrantTest;
                            $modelTest->load($data);
                            $modelsTest[$index][$indexTest] = $modelTest;
                            $valid = $modelTest->validate();
                        }
                    }
                }

                if ($valid) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelsMembers as $index => $modelMembers) {
                                if ($flag === false) {
                                    break;
                                }
                                $modelMembers->entrant_id = $model->id;
                                if (!($flag = $modelMembers->save(false))) {
                                    break;
                                }
                                $modelMembers = EntrantMembers::findOne(['id' => $modelMembers->id]);
                                if (isset($modelsTest[$index]) && is_array($modelsTest[$index])) {
                                    foreach ($modelsTest[$index] as $indexTest => $modelTest) {
                                        $modelTest->entrant_members_id = $modelMembers->id;
                                        if (!($flag = $modelTest->save(false))) {
                                            break;
                                        }
                                    }
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

            return $this->renderIsAjax('/entrant/applicants/_form_student', [
                    'model' => $model,
                    'modelsMembers' => (empty($modelsMembers)) ? [new EntrantMembers] : $modelsMembers,
                    'modelsTest' => (empty($modelsTest)) ? [[new EntrantTest]] : $modelsTest,
                    'readonly' => $readonly
                ]
            );

        } elseif ('history' == $mode && $objectId) {
            $model = Entrant::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['/entrant/default/applicants', 'id' => $id, 'objectId' => $objectId, 'mode' => 'view']];
            $data = new EntrantHistory($objectId);
            return $this->renderIsAjax('/history/index', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = Entrant::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = Entrant::findOne($objectId);

            if (!isset($model)) {
                throw new NotFoundHttpException("The Entrant was not found.");
            }

            $modelsMembers = $model->getEntrantMembersDefault();
            $modelsTest = [];
            $oldTest = [];

            if (!empty($modelsMembers)) {
                foreach ($modelsMembers as $index => $modelMembers) {
                    $tests = $modelMembers->getEntrantTestDefault();
                    $modelsTest[$index] = $tests;
                    $tests2 = $modelMembers->entrantTest;
                    $oldTest = ArrayHelper::merge(ArrayHelper::index($tests2, 'id'), $oldTest);
                }
            }
            if ($model->load(Yii::$app->request->post())) {
                // reset
                $modelsMembers = $model->entrantMembers;
                $modelsTest = [];

                $oldMembersIDs = ArrayHelper::map($modelsMembers, 'id', 'id');
                $modelsMembers = Model::createMultiple(EntrantMembers::class, $modelsMembers);
                Model::loadMultiple($modelsMembers, Yii::$app->request->post());
                $deletedMembersIDs = array_diff($oldMembersIDs, array_filter(ArrayHelper::map($modelsMembers, 'id', 'id')));

                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsMembers) && $valid;

                $testsIDs = [];
                if (isset($_POST['EntrantTest'][0][0])) {
                    foreach ($_POST['EntrantTest'] as $index => $tests) {
                        $testsIDs = ArrayHelper::merge($testsIDs, array_filter(ArrayHelper::getColumn($tests, 'id')));
                        foreach ($tests as $indexTest => $test) {
                            $data['EntrantTest'] = $test;
                            $modelTest = (isset($test['id']) && isset($oldTest[$test['id']])) ? $oldTest[$test['id']] : new EntrantTest;
                            $modelTest->load($data);
                            $modelsTest[$index][$indexTest] = $modelTest;
                            $valid = $modelTest->validate();
                        }
                    }
                }
                $oldTestIDs = ArrayHelper::getColumn($oldTest, 'id');
                $deletedTestsIDs = array_diff($oldTestIDs, $testsIDs);

                if ($valid) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedTestsIDs)) {
                                EntrantTest::deleteAll(['id' => $deletedTestsIDs]);
                            }
                            if (!empty($deletedMembersIDs)) {
                                EntrantMembers::deleteAll(['id' => $deletedMembersIDs]);
                            }

                            foreach ($modelsMembers as $index => $modelMembers) {
                                if ($flag === false) {
                                    break;
                                }
                                $modelMembers->entrant_id = $model->id;
                                if (!($flag = $modelMembers->save(false))) {
                                    break;
                                }
                                $modelMembers = EntrantMembers::findOne(['id' => $modelMembers->id]);
                                if (isset($modelsTest[$index]) && is_array($modelsTest[$index])) {
                                    foreach ($modelsTest[$index] as $indexTest => $modelTest) {
                                        $modelTest->entrant_members_id = $modelMembers->id;
                                        if (!($flag = $modelTest->save(false))) {
                                            break;
                                        }
                                    }
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

            return $this->renderIsAjax('/entrant/applicants/_form_student', [
                    'model' => $model,
                    'modelsMembers' => (empty($modelsMembers)) ? [new EntrantMembers] : $modelsMembers,
                    'modelsTest' => (empty($modelsTest)) ? [[new EntrantTest]] : $modelsTest,
                    'readonly' => $readonly
                ]
            );
        } else {
            $modelClass = 'common\models\entrant\Entrant';
            $searchModel = new EntrantSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }
            $params[$searchName]['student_id'] = $id;

            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('entrant', compact('dataProvider', 'searchModel'));
        }
    }

    public function actionStudyplan($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/student', 'Students'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['students/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual student plans'), 'url' => ['/students/default/studyplan', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление плана учащегося';
            $model = new Studyplan();
            $model->student_id = Yii::$app->request->get('id') ?: null;

            if ($model->load(Yii::$app->request->post())) {
                // validate all models
                $valid = $model->validate();
                //$valid = true;
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

            return $this->renderIsAjax('/studyplan/default/_form', [
                'model' => $model,
                'modelsStudyplanSubject' => [new StudyplanSubject],
                'readonly' => $readonly
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = Studyplan::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual student plans'), 'url' => ['/students/default/studyplan', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['/students/default/studyplan', 'id' => $id, 'objectId' => $objectId, 'mode' => 'view']];
            $data = new StudyplanHistory($objectId);
            return $this->renderIsAjax('/studyplan/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = Studyplan::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual student plans'), 'url' => ['/students/default/studyplan', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = Studyplan::findOne($objectId);

            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanSubject was not found.");
            }

            $modelsDependence = $model->studyplanSubject;

            if ($model->load(Yii::$app->request->post())) {

                $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
                $modelsDependence = Model::createMultiple(StudyplanSubject::class, $modelsDependence);
                Model::loadMultiple($modelsDependence, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependence, 'id', 'id')));

                // validate all models
                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsDependence) && $valid;

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                StudyplanSubject::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsDependence as $modelDependence) {
                                $modelDependence->studyplan_id = $model->id;
                                $modelDependence->subject_type_id = $modelDependence->subject_type_id != null ? $modelDependence->subject_type_id : $model->subject_type_id;

                                if (!($flag = $modelDependence->save(false))) {
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
            if (Yii::$app->request->post('submitAction') == 'doc_contract') {
                if ($model->subject_type_id == SubjectType::BASIS_FREE) {
                    $model->makeDocx(Studyplan::template_csf);
                } else {
                    $model->makeDocx(Studyplan::template_cs);
                }
            } elseif (Yii::$app->request->post('submitAction') == 'doc_statement') {
                $model->makeDocx(Studyplan::template_ss);
            }
            return $this->render('/studyplan/default/_form', [
                'model' => $model,
                'modelsStudyplanSubject' => (empty($modelsDependence)) ? [new StudyplanSubject] : $modelsDependence,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = Yii::t('art/studyplan', 'Individual student plans');
            $modelClass = 'common\models\studyplan\Studyplan';
            $searchModel = new StudyplanViewSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }
            $params[$searchName]['student_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('studyplan', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionDocument($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/student', 'Students'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['students/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/students/default/document', 'id' => $id]];
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
                'student_id' => $id
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['/students/default/document', 'id' => $id]];
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
                'student_id' => $id
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
                'student_id' => $id
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
//            ['label' => 'Монитор ученика', 'url' => ['/students/default/monitor', 'id' => $id]],
            ['label' => 'Карточка ученика', 'url' => ['/students/default/update', 'id' => $id]],
            ['label' => 'Планы учащегося', 'url' => ['/students/default/studyplan', 'id' => $id]],
            ['label' => 'Испытания', 'url' => ['/students/default/entrant', 'id' => $id]],
            ['label' => 'История обучения', 'url' => ['/students/default/education-history', 'id' => $id]],
            ['label' => 'Документы', 'url' => ['/students/default/document', 'id' => $id]],
        ];
    }
}