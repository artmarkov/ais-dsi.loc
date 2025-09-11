<?php

namespace frontend\controllers\teachers;

use backend\models\Model;
use common\models\creative\CreativeWorks;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use common\models\efficiency\TeachersEfficiency;
use yii\web\NotFoundHttpException;

/**
 * CreativeController
 */
class CreativeController extends MainController
{
    public $modelClass = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';

    public function actionIndex()
    {

        $query = $this->modelClass::find()->where(['like', 'teachers_list', $this->teachers_id]);

        $searchModel = new $this->modelSearchClass($query);
        $params = \Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('@backend/views/creative/default/index', compact('dataProvider', 'searchModel'));

    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     */
    public function actionView($id, $readonly = true)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The CreativeWorks was not found.");
        }

        $modelsEfficiency = $model->teachersEfficiency;

        return $this->render('@backend/views/creative/default/update', [
            'model' => $model,
            'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency()] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => $readonly
        ]);
    }
    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
//        $modelsEfficiency = [new TeachersEfficiency];

        if ($model->load(Yii::$app->request->post())) {

//            $modelsEfficiency = Model::createMultiple(TeachersEfficiency::class);
//            Model::loadMultiple($modelsEfficiency, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
//            $valid = Model::validateMultiple($modelsEfficiency) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                       /* foreach ($modelsEfficiency as $modelEfficiency) {
                            $modelEfficiency->item_id = $model->id;
                            $modelEfficiency->class = \yii\helpers\StringHelper::basename(get_class($model));
                            if (!($flag = $modelEfficiency->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }*/
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

        return $this->renderIsAjax('@backend/views/creative/default/create', [
            'model' => $model,
           // 'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => false
        ]);
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The CreativeWorks was not found.");
        }

        $modelsEfficiency = $model->teachersEfficiency;

        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->request->post('submitAction') == 'send_approve') {
                $model->doc_status = CreativeWorks::DOC_STATUS_WAIT;
                if ($model->sendApproveMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
                $model->doc_status = CreativeWorks::DOC_STATUS_MODIF;
            }
//            $oldIDs = ArrayHelper::map($modelsEfficiency, 'id', 'id');
//            $modelsEfficiency = Model::createMultiple(TeachersEfficiency::class, $modelsEfficiency);
//            Model::loadMultiple($modelsEfficiency, Yii::$app->request->post());
//            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsEfficiency, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
//            $valid = Model::validateMultiple($modelsEfficiency) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        /*if (!empty($deletedIDs)) {
                            TeachersEfficiency::deleteAll(['id' => $deletedIDs]);
                        }*/
                        /*foreach ($modelsEfficiency as $modelEfficiency) {
                            $modelEfficiency->class = \yii\helpers\StringHelper::basename(get_class($model));
                            $modelEfficiency->item_id = $model->id;
                            if (!($flag = $modelEfficiency->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }*/
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('@backend/views/creative/default/update', [
            'model' => $model,
          //  'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency()] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => $readonly
        ]);
    }
}