<?php

namespace artsoft\user\controllers;

use artsoft\controllers\admin\BaseController;
use common\models\user\User;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends MainController
{
    /**
     * @var User
     */
    public $modelClass = 'common\models\user\User';

    /**
     * @var UserSearch
     */
    public $modelSearchClass = 'artsoft\user\models\search\UserSearch';

    public $disabledActions = ['view'];
    
    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new User(['scenario' => 'newUser']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->renderIsAjax('create', compact('model'));
    }

    /**
     * @param int $id User ID
     *
     * @throws \yii\web\NotFoundHttpException
     * @return string
     */
    public function actionChangePassword($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('art/user', 'User not found'));
        }

        $model->scenario = 'changePassword';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art/auth', 'Password has been updated'));
            return $this->redirect(['change-password', 'id' => $model->id]);
        }

        return $this->renderIsAjax('changePassword', compact('model'));
    }
}