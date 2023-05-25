<?php

namespace frontend\controllers\entrant;

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
use common\models\history\EntrantGroupHistory;
use common\models\history\EntrantHistory;
use common\models\own\Department;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\entrant\EntrantComm model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\entrant\EntrantComm';
    public $modelSearchClass = 'common\models\entrant\search\EntrantCommSearch';
    public $modelHistoryClass = 'common\models\history\EntrantCommHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/entrant/default';
        parent::init();
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id, $readonly = true)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionApplicants($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Comms'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['entrant/default/view', 'id' => $id]];

        if ($objectId) {

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


    public function actionGroups()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = Entrant::getCommGroupById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actionDepartment()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = Department::getDepartmentListById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
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
            ['label' => 'Экзаменационная ведомость', 'url' => ['/entrant/default/applicants', 'id' => $id]],
        ];

    }

    public function beforeAction($action)
    {

        return parent::beforeAction($action);
    }
}