<?php

namespace backend\controllers\own;

use common\models\history\InvoicesHistory;

/**
 * InvoicesController implements the CRUD actions for common\models\own\Invoices model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\own\Invoices';
    public $modelSearchClass = '';

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new InvoicesHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}