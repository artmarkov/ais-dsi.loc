<?php

namespace artsoft\user\controllers;

use artsoft\models\User;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends MainController
{
    const ORIGINAL_USER_SESSION_KEY = 'original_user';

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

    /**
     * @param null $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionImpersonate($id = null)
    {
        if (!$id && Yii::$app->session->has(self::ORIGINAL_USER_SESSION_KEY)) {
            $user = $this->findModel(Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY));

            Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
        } else {
            if (!Yii::$app->user->identity->isAdmin()) {
                throw new ForbiddenHttpException();
            }

            $user = $this->findModel($id);
            Yii::$app->session->set(self::ORIGINAL_USER_SESSION_KEY, Yii::$app->user->id);
        }

        Yii::$app->user->switchIdentity($user, 3600);
        $this->response->redirect('/');
    }

    /**
     * @param null $id
     * @return \yii\web\Response
     */
    public function actionSendLogin($id = null){




        return $this->goHome();
    }
}