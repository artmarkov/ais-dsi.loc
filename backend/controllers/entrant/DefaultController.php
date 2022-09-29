<?php

namespace backend\controllers\entrant;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\entrant\EntrantMembers;
use common\models\entrant\EntrantTest;
use common\models\entrant\Entrant;
use common\models\entrant\EntrantGroup;
use common\models\entrant\search\EntrantGroupSearch;
use common\models\entrant\search\EntrantSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\entrant\EntrantComm model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\entrant\EntrantComm';
    public $modelSearchClass = 'common\models\entrant\search\EntrantCommSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                    $this->getSubmitAction($model);
                }
            }
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction($model);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionApplicants($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Comms'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['entrant/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление поступающего';
            $model = new Entrant();
            if (!Yii::$app->request->get('id')) {
                throw new NotFoundHttpException("Отсутствует обязательный параметр GET id.");
            }
            $model->comm_id = Yii::$app->request->get('id') ?: null;

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

            return $this->renderIsAjax('/entrant/applicants/_form', [
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
            return $this->renderIsAjax('/entrant/default/history', compact(['model', 'data']));

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

            return $this->renderIsAjax('/entrant/applicants/_form', [
                    'model' => $model,
                    'modelsMembers' => (empty($modelsMembers)) ? [new EntrantMembers] : $modelsMembers,
                    'modelsTest' => (empty($modelsTest)) ? [[new EntrantTest]] : $modelsTest,
                    'readonly' => $readonly
                ]
            );
        } else {
            $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Applicants');
            $modelClass = 'common\models\entrant\Entrant';
            $searchModel = new EntrantSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }
            $params[$searchName]['comm_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('applicants', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionGroup($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Comms'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['entrant/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Groups'), 'url' => ['/entrant/default/group', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление группы';
            $model = new EntrantGroup();
            $model->comm_id = Yii::$app->request->get('id') ?: null;
            $model->prep_flag = 1;

            if ($model->load(Yii::$app->request->post())) {
                $valid = $model->validate();
                if ($valid) {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                        $this->getSubmitAction($model);
                    }
                }
            }

            return $this->renderIsAjax('/entrant/group/_form', [
                'model' => $model,
                'readonly' => $readonly
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = EntrantGroup::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Groups'), 'url' => ['/entrant/default/group', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['/entrant/default/group', 'id' => $id, 'objectId' => $objectId, 'mode' => 'view']];
            $data = new EntrantGroupHistory($objectId);
            return $this->renderIsAjax('/entrant/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = EntrantGroup::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Groups'), 'url' => ['/entrant/default/group', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = EntrantGroup::findOne($objectId);

            if (!isset($model)) {
                throw new NotFoundHttpException("The EntrantGroup was not found.");
            }

            if ($model->load(Yii::$app->request->post())) {
                $valid = $model->validate();
                if ($valid) {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                        $this->getSubmitAction($model);
                    }
                }
            }

            return $this->renderIsAjax('/entrant/group/_form', [
                'model' => $model,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Entrant Groups');
            $modelClass = 'common\models\entrant\Entrant';
            $searchModel = new EntrantGroupSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }
            $params[$searchName]['comm_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('group', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionStat($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Comms'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['entrant/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление поступающего';
            $model = new Entrant();
            $model->comm_id = Yii::$app->request->get('id') ?: null;

            if ($model->load(Yii::$app->request->post())) {
                $valid = $model->validate();
                if ($valid) {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                        $this->getSubmitAction($model);
                    }
                }
            }

            return $this->renderIsAjax('/entrant/applicants/_form', [
                'model' => $model,
                'readonly' => $readonly
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = Entrant::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Applicants'), 'url' => ['/entrant/default/applicants', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['/entrant/default/applicants', 'id' => $id, 'objectId' => $objectId, 'mode' => 'view']];
            $data = new EntrantHistory($objectId);
            return $this->renderIsAjax('/entrant/default/history', compact(['model', 'data']));

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

            if ($model->load(Yii::$app->request->post())) {
                $valid = $model->validate();
                if ($valid) {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                        $this->getSubmitAction($model);
                    }
                }
            }

            return $this->renderIsAjax('/entrant/applicants/_form', [
                'model' => $model,
                'readonly' => $readonly
            ]);


        } else {
            $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Entrant Test');
            $modelClass = 'common\models\entrant\Entrant';
            $searchModel = new EntrantSearch();

            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }
            $params[$searchName]['comm_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('applicants', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getMenu($id)
    {
        $model = $this->findModel($id);
        return [
            ['label' => 'Карточка вступительных экзаменов', 'url' => ['/entrant/default/update', 'id' => $id]],
            ['label' => 'Поступающие', 'url' => ['/entrant/default/applicants', 'id' => $id]],
            ['label' => 'Экзаменационные группы', 'url' => ['/entrant/default/group', 'id' => $id]],
            ['label' => 'Экзаменационная ведомость', 'url' => ['/entrant/default/stat', 'id' => $id]],
        ];

    }
}