<?php

namespace frontend\controllers\service;

use common\models\service\UsersCard;
use Yii;
use yii\helpers\StringHelper;

/**
 * DefaultController implements the CRUD actions for common\models\service\UsersCard model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\service\UsersCard';
    public $modelSearchClass = 'common\models\service\search\ServiceCardViewSearch';
    public $modelHistoryClass = 'common\models\history\UsersCardHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/service/default';
        parent::init();
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        if (!Yii::$app->request->get('user_common_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET user_common_id.");
        }
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->user_common_id = Yii::$app->request->get('user_common_id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $searchModel = new $this->modelSearchClass();
        $params = $this->getParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }
}