<?php

namespace frontend\controllers\entrant;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\entrant\EntrantComm;
use common\models\entrant\EntrantMembers;
use common\models\entrant\EntrantTest;
use common\models\entrant\Entrant;
use common\models\entrant\search\EntrantCommSearch;
use common\models\entrant\search\EntrantSearch;
use Yii;
use yii\data\ActiveDataProvider;
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

    public function init()
    {
        $this->viewPath = '@backend/views/entrant/default';
        parent::init();
    }

    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $userId = Yii::$app->user->identity->getId();
        $query = EntrantComm::find()->where(new \yii\db\Expression("{$userId} = any (string_to_array(members_list::text, ',')::int[])"));
        $searchModel = new EntrantCommSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
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

                $modelsMembers = Model::createMultiple(EntrantMembers::class, $modelsMembers);
                Model::loadMultiple($modelsMembers, Yii::$app->request->post());

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
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/entrant/applicants/_form', [
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




    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getMenu($id)
    {
        $model = $this->findModel($id);
        return [
            ['label' => 'Карточка вступительных экзаменов', 'url' => ['/entrant/default/view', 'id' => $id]],
            ['label' => 'Экзаменационная ведомость', 'url' => ['/entrant/default/applicants', 'id' => $id]],
        ];

    }

    public function beforeAction($action)
    {

        return parent::beforeAction($action);
    }
}