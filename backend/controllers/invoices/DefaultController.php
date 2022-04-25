<?php

namespace backend\controllers\invoices;

use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * StudyplanInvoicesController implements the CRUD actions for common\models\studyplan\StudyplanInvoices model.
 */
class DefaultController extends BaseController
{
    public $modelClass       = 'common\models\studyplan\StudyplanInvoices';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanInvoicesViewSearch';

    public function actionIndex()
    {
        $searchModel = new StudyplanInvoicesViewSearch();

        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }
}