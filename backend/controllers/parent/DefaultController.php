<?php

namespace backend\controllers\parent;

use common\models\user\UserCommon;
use common\models\user\UserFamily;
use Yii;
use artsoft\models\User;
use yii\data\ActiveDataProvider;
Use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\user\UserCommon model.
 */
class DefaultController extends \backend\controllers\DefaultController
{

    public $modelClass = 'common\models\user\UserCommon';
    public $modelSearchClass = '';

    /**
     * Lists all UserCommon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $dataProvider = new ActiveDataProvider([
            'query' => $model = \common\models\user\UserCommon::find()->andWhere(['user_category' => User::USER_CATEGORY_PARENTS]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array|string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {

            $model->user_category = User::USER_CATEGORY_PARENTS;
            $model->status = User::STATUS_INACTIVE;

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been created.'));
                return $this->redirect($this->getRedirectPage('update', $model));
            }
        }

        return $this->renderIsAjax('create', compact('model'));
    }


    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;


        $model = $this->findModel(['id' => $id, 'user_category' => User::USER_CATEGORY_PARENTS]);

        if (!isset($model)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
            }
        }
        return $this->renderIsAjax($this->updateView, compact('model'));
    }

    /**
     * Добавляется родитель в базу и формируется родственная связь
     * Запускается из формы редактирования model Student из Модального окна parent-modal
     * Модал находится в layouts main
     */
    public function actionCreateFamily()
    {

        $modelFamily = new UserFamily();

        if ($modelFamily->load(Yii::$app->request->post())) {

            $user_slave_id = $modelFamily->user_slave_id;
            $user_slave_id != 0 ? $model = UserCommon::findOne($user_slave_id) : $model = new UserCommon();

            if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return \yii\widgets\ActiveForm::validate($model, $modelFamily);
            } elseif ($model->load(Yii::$app->request->post())) {

                $model->user_category = User::USER_CATEGORY_PARENTS;
                if ($model->isNewRecord)
                    $model->status = User::STATUS_INACTIVE;

                if ($model->save()) {

                    $modelFamily->user_slave_id = $model->id;

                    if ($modelFamily->save()) {
                        Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                } else {

                }
            }
        } else {
            throw new HttpException(404, 'Page not found');
        }
    }

    /**
     * Вызывается методом Ajax из main.js
     */
    public function actionInitFamily()
    {
        $id = Yii::$app->request->get('id');
        $user_slave_id = Yii::$app->request->get('user_slave_id');
        $user_slave_id != 0 ? $model = UserCommon::findOne($user_slave_id) : $model = new UserCommon();
        $modelFamily = new UserFamily();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $modelFamily->user_main_id = $id;
        $modelFamily->user_slave_id = $user_slave_id;

        $this->layout = false;
        return $this->renderIsAjax('parents-modal', ['model' => $model, 'modelFamily' => $modelFamily]);
    }

}
