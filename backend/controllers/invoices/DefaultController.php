<?php

namespace backend\controllers\invoices;

use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\web\NotFoundHttpException;

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

    public function actionCreate()
    {
        if (!Yii::$app->request->get('studyplan_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_id.");
        }
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->studyplan_id = Yii::$app->request->get('studyplan_id');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }
}