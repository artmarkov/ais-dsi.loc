<?php

namespace backend\controllers\creative;

use Yii;
use common\models\efficiency\TeachersEfficiency;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * DefaultController implements the CRUD actions for common\models\creative\CreativeWorks model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';
    public $modelHistoryClass = 'common\models\history\CreativeHistory';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelsEfficiency = [new TeachersEfficiency];

        if ($model->load(Yii::$app->request->post())) {

            $modelsEfficiency = Model::createMultiple(TeachersEfficiency::class);
            Model::loadMultiple($modelsEfficiency, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsEfficiency) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                        foreach ($modelsEfficiency as $modelEfficiency) {
                            $modelEfficiency->item_id = $model->id;
                            $modelEfficiency->class = \yii\helpers\StringHelper::basename(get_class($model));
                            if (!($flag = $modelEfficiency->save(false))) {
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

        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The CreativeWorks was not found.");
        }

        $modelsEfficiency = $model->teachersEfficiency;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsEfficiency, 'id', 'id');
            $modelsEfficiency = Model::createMultiple(TeachersEfficiency::class, $modelsEfficiency);
            Model::loadMultiple($modelsEfficiency, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsEfficiency, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsEfficiency) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TeachersEfficiency::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsEfficiency as $modelEfficiency) {
                            $modelEfficiency->class = \yii\helpers\StringHelper::basename(get_class($model));
                            $modelEfficiency->item_id = $model->id;
                            if (!($flag = $modelEfficiency->save(false))) {
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
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

}