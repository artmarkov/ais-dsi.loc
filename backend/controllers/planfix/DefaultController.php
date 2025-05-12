<?php

namespace backend\controllers\planfix;

use common\models\history\PlanfixHistory;
use common\models\planfix\Planfix;
use common\models\planfix\PlanfixActivity;
use Yii;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\planfix\Planfix model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\planfix\Planfix';
    public $modelSearchClass = 'common\models\planfix\search\PlanfixSearch';

    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Planfix'), 'url' => ['planfix/default/index']];
        $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new Planfix();
        $modelsItems = [new PlanfixActivity()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItems = Model::createMultiple(PlanfixActivity::class);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
//            $valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                        foreach ($modelsItems as $modelItems) {
                            $modelItems->planfix_id = $model->id;
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
                } catch (\Exception $e) {
                    print_r($e->errorInfo);
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new PlanfixActivity] : $modelsItems,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Planfix'), 'url' => ['planfix/default/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = Planfix::findOne($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The Planfix was not found.");
        }
        $modelsItems = $model->planfixActivities;
        if ($model->load(Yii::$app->request->post())) {
       // echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>';

            $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
            $modelsItems = Model::createMultiple(PlanfixActivity::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItems, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
//            $valid =true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            PlanfixActivity::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItems as $modelItems) {
                            $modelItems->planfix_id = $model->id;
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
                } catch (\Exception $e) {
                    print_r($e->errorInfo);
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new PlanfixActivity] : $modelsItems,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param int $id
     * @return mixed|string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionHistory($id)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art', 'Planfix'), 'url' => ['planfix/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['planfix/default/update', 'id' => $model->id]];
        $data = new PlanfixHistory($id);
        return $this->renderIsAjax('history.php', compact(['model', 'data']));

    }

}