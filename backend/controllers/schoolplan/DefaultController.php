<?php

namespace backend\controllers\schoolplan;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * ActivitiesPlanController implements the CRUD actions for common\models\activities\ActivitiesPlan model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\schoolplan\Schoolplan';
    public $modelSearchClass = 'common\models\schoolplan\search\SchoolplanSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
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
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка мероприятия', 'url' => ['/schoolplan/default/update', 'id' => $id]],
            ['label' => 'Показатели эффективности', 'url' => ['/schoolplan/default/teachers-efficiency', 'id' => $id]],
            ['label' => 'Протокол мероприятия', 'url' => ['/schoolplan/default/protocol', 'id' => $id]],
        ];
    }
}