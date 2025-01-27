<?php

namespace backend\controllers\concourse;

use backend\models\Model;
use common\models\concourse\ConcourseAnswers;
use common\models\concourse\ConcourseCriteria;
use common\models\concourse\ConcourseItem;
use common\models\concourse\ConcourseValue;
use common\models\concourse\search\ConcourseCriteriaSearch;
use common\models\concourse\search\ConcourseItemSearch;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use himiklab\sortablegrid\SortableGridAction;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\concourse\Concourse model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\concourse\Concourse';
    public $modelSearchClass = 'common\models\concourse\search\ConcourseSearch';
    public $modelHistoryClass = '';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, [
                'model' => $model,
                'readonly' => false
            ]
        );
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The Question was not found.");
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionConcourseCriteria($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The Concourse was not found.");
        }

        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['concourse/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['concourse/default/update', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Критерии оценки конкурса', 'url' => ['/concourse/default/concourse-criteria', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление критерия';

            $modelCriteria = new ConcourseCriteria();
            $modelCriteria->concourse_id = $model->id;

            if ($modelCriteria->load(Yii::$app->request->post()) && $modelCriteria->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelCriteria);
            }

            return $this->renderIsAjax('@backend/views/concourse/concourse-criteria/_form.php', [
                'model' => $modelCriteria,
            ]);

        } elseif ('delete' == $mode && $objectId) {
            $modelCriteria = ConcourseCriteria::findOne($objectId);
            $modelCriteria->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelCriteria));

        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Критерии оценки конкурса', 'url' => ['/concourse/default/concourse-criteria', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $modelCriteria = ConcourseCriteria::findOne($objectId);
            if (!isset($modelCriteria)) {
                throw new NotFoundHttpException("The ConcourseCriteria was not found.");
            }

            if ($modelCriteria->load(Yii::$app->request->post()) && $modelCriteria->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelCriteria);
            }

            return $this->renderIsAjax('@backend/views/concourse/concourse-criteria/_form.php', [
                'model' => $modelCriteria,
            ]);

        } else {
            $searchModel = new ConcourseCriteriaSearch();
            $searchName = \yii\helpers\StringHelper::basename($searchModel::className());
            $params = $this->getParams();
            $params[$searchName]['concourse_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('concourse-criteria', compact('dataProvider', 'searchModel'));
        }
    }

    public function actionConcourseItem($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The Concourse was not found.");
        }

        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['concourse/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['concourse/default/update', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсные работы', 'url' => ['/concourse/default/concourse-item', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление работы';

            $modelItem = new ConcourseItem();
            $modelItem->concourse_id = $model->id;

            if ($modelItem->load(Yii::$app->request->post()) && $modelItem->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelItem);
            }

            return $this->renderIsAjax('@backend/views/concourse/concourse-item/_form.php', [
                'model' => $modelItem,
            ]);

        } elseif ('delete' == $mode && $objectId) {
            $modelItem = ConcourseItem::findOne($objectId);
            $modelItem->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelItem));

        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсные работы', 'url' => ['/concourse/default/concourse-item', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $modelItem = ConcourseItem::findOne($objectId);
            if (!isset($modelItem)) {
                throw new NotFoundHttpException("The ConcourseCriteria was not found.");
            }

            if ($modelItem->load(Yii::$app->request->post()) && $modelItem->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelItem);
            }
            $modelsAnswers = new ConcourseAnswers(['id' => $id, 'objectId' => $objectId]);

            return $this->renderIsAjax('@backend/views/concourse/concourse-item/_form.php', [
                'model' => $modelItem,
                'data' => $modelsAnswers->getData(),
            ]);

        } else {
            $searchModel = new ConcourseItemSearch();
            $searchName = \yii\helpers\StringHelper::basename($searchModel::className());
            $params = $this->getParams();
            $params[$searchName]['concourse_id'] = $id;
            $dataProvider = $searchModel->search($params);
            $modelsAnswers = new ConcourseAnswers(['id' => $id]);

            return $this->renderIsAjax('concourse-item', compact('dataProvider', 'searchModel', 'modelsAnswers'));
        }
    }

    public function actionConcourseAnswers($id, $objectId = null, $mode = null, $readonly = false)
    {
        if (!isset($_GET['userId'])) {
            throw new NotFoundHttpException("The userId was not found.");
        }
        $model = $this->findModel($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The Concourse was not found.");
        }
        $userId = $_GET['userId'];

        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['concourse/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['concourse/default/update', 'id' => $id]];

        if ('delete' == $mode && $objectId) {
            ConcourseValue::deleteAll(['users_id' => $userId,'concourse_item_id' => $objectId]);
            Yii::$app->session->setFlash('info', 'Оценки успешно удалены.');
            $this->redirect(['/concourse/default/concourse-item', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']);

        } elseif ($objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсные работы', 'url' => ['/concourse/default/concourse-item', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['/concourse/default/concourse-item', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $this->view->params['breadcrumbs'][] = 'Карточка оценки конкурсной работы';

            $modelsAnswers = new ConcourseAnswers(['id' => $id, 'objectId' => $objectId, 'userId' => $userId]);
            $modelsItems = $modelsAnswers->getAnswersConcourseUsers();
            if (isset($_POST['ConcourseValue'])) {
                Model::loadMultiple($modelsItems, Yii::$app->request->post());

                // validate all models
                $valid = Model::validateMultiple($modelsItems);

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $flag = true;
                        foreach ($modelsItems as $modelItems) {
                            if (!($flag = $modelItems->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                            $this->redirect(['/concourse/default/concourse-item', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
                    }
                }
            }
            return $this->renderIsAjax('@backend/views/concourse/concourse-answers/_form.php', [
                'model' => $modelsAnswers,
                'id' => $id,
                'objectId' => $objectId,
                'modelsItems' => $modelsItems,
            ]);

        }
    }

    public function actionStat()
    {

    }

    /**
     * Установка оценки
     * @return string|null
     * @throws \yii\db\Exception
     */
    public function actionSetMark()
    {
        $users_id = $_GET['users_id'];
        $concourse_item_id = $_GET['concourse_item_id'];
        $concourse_criteria_id = $_GET['concourse_criteria_id'];
        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['concourse_mark'])) {
                $model = ConcourseValue::find()->where(['users_id' => $users_id])->andWhere(['concourse_item_id' => $concourse_item_id])->andWhere(['concourse_criteria_id' => $concourse_criteria_id])->one() ?? new ConcourseValue();
                $model->concourse_mark = $_POST['concourse_mark'];
                $model->users_id = $_GET['users_id'];
                $model->concourse_item_id = $_GET['concourse_item_id'];
                $model->concourse_criteria_id = $_GET['concourse_criteria_id'];
                $model->save(false);
                return Json::encode(['output' => $model->concourse_mark, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }

    /**
     * action sort for himiklab\sortablegrid\SortableGridBehavior
     * @return type
     */
    public function actions()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => 'common\models\concourse\ConcourseCriteria',
            ],
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка конкурса', 'url' => ['/concourse/default/update', 'id' => $id]],
            ['label' => 'Критерии оценки конкурса', 'url' => ['/concourse/default/concourse-criteria', 'id' => $id]],
            ['label' => 'Конкурсные работы', 'url' => ['/concourse/default/concourse-item', 'id' => $id]],
//            ['label' => 'Статистика и результаты конкурса', 'url' => ['/concourse/default/stat', 'id' => $id]],
        ];
    }
}