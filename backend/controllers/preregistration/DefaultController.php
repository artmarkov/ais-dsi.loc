<?php

namespace backend\controllers\preregistration;

use common\models\teachers\search\TeachersLoadViewSearch;
use Yii;
use yii\helpers\StringHelper;

/**
 * DefaultController implements the CRUD actions for common\models\education\EntrantPreregistrations model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\education\EntrantPreregistrations';
    public $modelSearchClass = 'common\models\education\search\EntrantPreregistrationsSearch';

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model_date = $this->modelDate;

        $searchModel = new $this->modelSearchClass;

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['plan_year'] = $model_date->plan_year;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->student_id = $_GET['id'] ?? null;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }
}