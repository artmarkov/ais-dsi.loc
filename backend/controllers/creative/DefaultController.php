<?php

namespace backend\controllers\creative;

use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\creative\CreativeWorks model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';

    /**
     * @param int $id
     * @return mixed|string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, compact(['model', 'readonly']));
    }

}