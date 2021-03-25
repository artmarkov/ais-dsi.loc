<?php

namespace artsoft\user\controllers;

use artsoft\models\User;
use common\models\history\UserHistory;
use http\Url;
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
    public $modelClass = 'artsoft\models\User';

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
     * @return string
     * @throws \yii\web\NotFoundHttpException
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

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new UserHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}