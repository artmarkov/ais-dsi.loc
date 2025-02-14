<?php

namespace frontend\controllers\concourse;

use artsoft\models\User;
use backend\models\Model;
use common\models\concourse\Concourse;
use common\models\concourse\ConcourseAnswers;
use common\models\concourse\ConcourseItem;
use common\models\concourse\search\ConcourseItemSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\concourse\Concourse model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\concourse\Concourse';
    public $modelSearchClass = 'common\models\concourse\search\ConcourseSearch';

    public $freeAccessActions = ['index', 'new', 'success', 'concourse-item'];

    public $users_id;

    public function init()
    {
        $this->viewPath = '@frontend/views/concourse/default';

        parent::init();
    }

    public function actionIndex()
    {
        if (!User::hasRole(['teacher', 'department'], false)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $query = Concourse::find()
            ->where(['<=', 'timestamp_in', time()])
            ->andWhere(['>=', 'timestamp_out', time() - 86400])
            ->andWhere(['=', 'status', Concourse::STATUS_ACTIVE]);
        $searchModel = false;
        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    public function actionConcourseItem($id, $objectId = null, $mode = null, $readonly = false)
    {
        if (!User::hasRole(['teacher', 'department'], false)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $model = Concourse::find()
            ->where(['<=', 'timestamp_in', time()])
            ->andWhere(['>=', 'timestamp_out', time() - 86400])
            ->andWhere(['=', 'status', Concourse::STATUS_ACTIVE])
            ->andWhere(['=', 'id', $id])->one();
        if (!isset($model)) {
            $model = Concourse::find()->where(['=', 'id', $id])->one();
            if (isset($model)) {
                $message = 'Форма будет активна ' . Yii::$app->formatter->asDate($model->timestamp_in);
                return $this->renderIsAjax('validate-warning', ['message' => $message]);
            } else {
                throw new NotFoundHttpException("The Concourse was not found.");
            }
        }

        $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['/concourse/default/index']];

        if ('view' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Конкурсные работы', 'url' => ['/concourse/default/concourse-item', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $modelItem = ConcourseItem::findOne($objectId);
            if (!isset($modelItem)) {
                throw new NotFoundHttpException("The ConcourseItem was not found.");
            }

            $modelsAnswers = new ConcourseAnswers(['id' => $id, 'objectId' => $objectId, 'userId' => $this->users_id]);
            if (!$modelsAnswers->isUsersItem()) {
                $message = 'Вы не можете оценивать данную работу.';
                return $this->renderIsAjax('validate-warning', ['message' => $message]);
            }
            $modelsItems = $modelsAnswers->getAnswersConcourseUsers($this->users_id);
//            echo '<pre>' . print_r($modelsItems, true) . '</pre>'; die();
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
                            $this->redirect(['/concourse/default/concourse-item', 'id' => $id]);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
                    }
                }
            }
            return $this->renderIsAjax('view', [
                'model' => $modelsAnswers,
                'modelItem' => $modelItem,
                'id' => $id,
                'objectId' => $objectId,
                'modelsItems' => $modelsItems,
            ]);

        } else {
            $modelsAnswers = new ConcourseAnswers(['id' => $id, 'userId' => $this->users_id]);
            if (!$modelsAnswers->isUsers()) {
                $message = 'Вы не являетесь участником конкурса';
                return $this->renderIsAjax('validate-warning', ['message' => $message]);
            }
            $query = ConcourseItem::find()->where(['concourse_id' => $id]);
           /* if ($model->authors_ban_flag) {
                $query = $query->andWhere(new \yii\db\Expression("{$this->users_id} <> all (string_to_array(authors_list, ',')::int[])"));
            }*/
            $searchModel = new ConcourseItemSearch($query);
            $params = $this->getParams();
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('concourse-item', compact('dataProvider', 'searchModel', 'modelsAnswers', 'model'));
        }
    }


    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) { // Если по ссылке проходит незалогиненный пользователь
            $this->redirect('/auth/default/login');
        } else {
            if (!User::hasRole(['teacher', 'department'], false)) {
                throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }
            if (!Yii::$app->user->isGuest) {
                $this->users_id = Yii::$app->user->identity->getId();
            }
        }
        return parent::beforeAction($action);
    }

}