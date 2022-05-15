<?php

namespace artsoft\user\controllers;

use artsoft\models\User;
use artsoft\models\UserIdentity;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends MainController
{
    const ORIGINAL_USER_SESSION_KEY = 'original_user';

    public $freeAccessActions = ['impersonate'];

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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionImpersonate($id = null)
    {
        if (!$id && Yii::$app->session->has(self::ORIGINAL_USER_SESSION_KEY)) {
            $user = $this->findModel(Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY));

            Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
            Yii::$app->user->logout(true);
        } else {
            if (!Yii::$app->user->identity->isAdmin()) {
                throw new ForbiddenHttpException();
            }

            $user = $this->findModel($id);
            Yii::$app->session->set(self::ORIGINAL_USER_SESSION_KEY, Yii::$app->user->id);
        }

        Yii::$app->user->login($user);
        $this->redirect('/');
    }

    /**
     * @param null $id
     * @return \yii\web\Response
     */
    public function actionSendLogin($id = null)
    {
        $user = User::findOne($id);
        $user->generateConfirmationToken();

        if (!$user->save(false) && !$this->sendLoginToEmail($user)) {
            Yii::$app->session->setFlash('error', 'Ошибка отправки регистрационных данных.');
        } else {
            Yii::$app->session->setFlash('success', 'Регистрационные данные успешно отправлены.');

        }
        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->referrer);
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function sendLoginToEmail($user)
    {
        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['password-reset'],
            ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'Password reset for') . ' ' . Yii::$app->name)
            ->send();
    }

    /**
     * @param $action
     * @param null $model
     * @return array
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'index':
            case 'delete':
                return ['index'];
                break;
            case 'create':
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            default:
                return ['index'];
        }
    }

}