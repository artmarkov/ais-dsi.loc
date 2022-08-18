<?php

namespace backend\controllers\entrant;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DefaultController implements the CRUD actions for common\models\entrant\EntrantComm model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\entrant\EntrantComm';
    public $modelSearchClass = 'common\models\entrant\search\EntrantCommSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                    $this->getSubmitAction($model);
                }
            }
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction($model);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }
    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getMenu($id)
    {
        $model = $this->findModel($id);
        return [
            ['label' => 'Карточка вступительных экзаменов', 'url' => ['/entrant/default/update', 'id' => $id]],
            ['label' => 'Поступающие', 'url' => ['/entrant/entrant/index', 'id' => $id]],
            ['label' => 'Экзаменационные группы', 'url' => ['/entrant/group/index', 'id' => $id]],
            ['label' => 'Экзаменационная ведомость', 'url' => ['/entrant/stat/index', 'id' => $id]],
        ];

    }
}