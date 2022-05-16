<?php

namespace artsoft\auth\controllers;


use artsoft\auth\assets\AvatarAsset;
use artsoft\auth\AuthModule;
use artsoft\auth\helpers\AvatarHelper;
use artsoft\auth\models\Auth;
use artsoft\auth\models\forms\ConfirmEmailForm;
use artsoft\auth\models\forms\FindingForm;
use artsoft\auth\models\forms\ProfileForm;
use artsoft\auth\models\forms\ResetPasswordForm;
use artsoft\auth\models\forms\UpdatePasswordForm;
use artsoft\auth\models\forms\SignupForm;
use artsoft\auth\models\forms\LoginForm;
use artsoft\auth\models\forms\SignupFindForm;
use artsoft\auth\models\forms\SetEmailForm;
use artsoft\auth\models\forms\SetPasswordForm;
use artsoft\auth\models\forms\SetUsernameForm;
use artsoft\controllers\BaseController;
use artsoft\models\User;
use artsoft\components\AuthEvent;
use artsoft\widgets\ActiveForm;
use common\models\user\UserCommon;
use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\components\NumericCaptcha;


class DefaultController extends BaseController
{
    /**
     * @var array
     */
    public $freeAccessActions = ['login', 'logout', 'captcha', 'oauth', 'signup', 'finding',
        'confirm-email', 'confirm-registration-email', 'confirm-email-receive',
        'reset-password', 'reset-password-request', 'update-password', 'set-email',
        'set-username', 'set-password', 'profile', 'upload-avatar', 'remove-avatar',
        'unlink-oauth'];

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => NumericCaptcha::className(),
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'oauth' => [
                'class' => 'artsoft\auth\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'upload-avatar' => ['post'],
                    'remove-avatar' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @param $client
     * @return \yii\console\Response|Response
     * @throws \yii\db\Exception
     */
    public function onAuthSuccess($client)
    {
        $source = $client->getId();
        $userAttributes = $client->getUserAttributes();

        if (!isset($this->module->attributeParsers[$source])) {
            throw \yii\base\InvalidConfigException("There are no settings for '{$source}' in the AuthModule::attributeParsers.");
        }

        $attributes = $this->module->attributeParsers[$source]($userAttributes);
        Yii::$app->session->set(AuthModule::PARAMS_SESSION_ID, $attributes);

        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $attributes['source'],
            'source_id' => $attributes['source_id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // signup
                if (isset($attributes['email']) && $attributes['email'] && User::find()->where(['email' => $attributes['email']])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('art/auth', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $client->getTitle()]),
                    ]);
                    Yii::$app->getResponse()->redirect(['auth/default/login']);
                } else {
                    return $this->createUser($attributes);
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $attributes['source'],
                    'source_id' => $attributes['source_id'],
                ]);
                $auth->save();
            }
        }
    }

    /**
     * @return array|string|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSetEmail()
    {
        $attributes = Yii::$app->session->get(AuthModule::PARAMS_SESSION_ID);

        if (!Yii::$app->user->isGuest || !$attributes || !is_array($attributes)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = new SetEmailForm();

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            $attributes['email'] = $model->email;
            Yii::$app->session->set(AuthModule::PARAMS_SESSION_ID, $attributes);
            return $this->createUser($attributes);
        }

        return $this->renderIsAjax('set-email', compact('model'));
    }

    /**
     * @return array|string|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSetUsername()
    {
        $attributes = Yii::$app->session->get(AuthModule::PARAMS_SESSION_ID);

        if (!Yii::$app->user->isGuest || !$attributes || !is_array($attributes)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = new SetUsernameForm();

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            $attributes['username'] = $model->username;
            Yii::$app->session->set(AuthModule::PARAMS_SESSION_ID, $attributes);
            return $this->createUser($attributes);
        }

        return $this->renderIsAjax('set-username', compact('model'));
    }

    /**
     * @return array|string|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSetPassword()
    {
        $attributes = Yii::$app->session->get(AuthModule::PARAMS_SESSION_ID);

        if (!Yii::$app->user->isGuest || !$attributes || !is_array($attributes)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = new SetPasswordForm();

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            $attributes['password'] = $model->password;
            Yii::$app->session->set(AuthModule::PARAMS_SESSION_ID, $attributes);
            return $this->createUser($attributes);
        }

        return $this->renderIsAjax('set-password', compact('model'));
    }

    /**
     * @param $attributes
     * @return \yii\console\Response|Response
     * @throws \yii\db\Exception
     */
    protected function createUser($attributes)
    {
        $auth = [
            'source' => (string)$attributes['source'],
            'source_id' => (string)$attributes['source_id'],
        ];

        unset($attributes['source']);
        unset($attributes['source_id']);

        $attributes['repeat_password'] = isset($attributes['password']) ? $attributes['password'] : NULL;

        $user = new User($attributes);

        $user->setScenario(User::SCENARIO_NEW_USER);
        $user->generateAuthKey();
        //$user->generatePasswordResetToken();

        $transaction = $user->getDb()->beginTransaction();

        if ($user->save()) {

            $auth = new Auth([
                'user_id' => $user->id,
                'source' => $auth['source'],
                'source_id' => $auth['source_id'],
            ]);

            if ($auth->save()) {
                $transaction->commit();
                Yii::$app->user->login($user);
            } else {
                Yii::$app->session->setFlash('error', 'Error 901: ' . Yii::t('art/auth', "Authentication error occurred."));
                return Yii::$app->response->redirect(Url::to(['/auth/default/login']));
            }
        } else {

            $errors = $user->getErrors();
            $fields = ['username', 'email', 'password'];

            foreach ($fields as $field) {
                if (isset($errors[$field])) {
                    Yii::$app->session->setFlash('error', $user->getFirstError($field));
                    return Yii::$app->response->redirect(Url::to(['/auth/default/set-' . $field]));
                }
            }

            Yii::$app->session->setFlash('error', 'Error 902: ' . Yii::t('art/auth', "Authentication error occurred."));
            return Yii::$app->response->redirect(Url::to(['/auth/default/login']));
        }

        Yii::$app->session->remove(AuthModule::PARAMS_SESSION_ID);
        return Yii::$app->response->redirect(Url::to(['/']));
    }

    /**
     * Login form
     *
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = new LoginForm();

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->login()) {
            return $this->goBack();
        }

        return $this->renderIsAjax('login', compact('model'));
    }

    /**
     * Logout and redirect to home page
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Yii::$app->homeUrl);
    }


    /**
     * Finding User before registration
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionFinding()
    {
        if (!Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('art', 'Page not found.'));
        }
        $model = new FindingForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $userCommon = FindingForm::findByFio($model);
//            echo '<pre>' . print_r($userCommon, true) . '</pre>'; die();
            if ($userCommon) { // если нашли запись
                return $this->redirect(['signup', 'auth_key' => $userCommon->user->auth_key]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('art/auth', "User not found or blocked in the system"));
            }
        }
        return $this->render('finding', compact('model'));
    }

    /**
     * Signup page after finding user params
     *
     * @param $auth_key
     * @return bool|string|Response
     * @throws NotFoundHttpException
     */
    public function actionSignup($auth_key)
    {
        if (!Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('art', 'Page not found.'));
        }
        $user = User::findByAuthKey($auth_key);
        if (!$user) {
            Yii::$app->session->setFlash('error', Yii::t('art/auth', "Token not found. It may be expired"));
            return $this->goHome();
        }
        $model = new SignupForm();

        $model->setAttributes(
            [
                'username' => $user->username,
                'id' => $user->id,
            ]
        );
        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model->validate();
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            // Trigger event "before registration" and checks if it's valid
            if ($this->triggerModuleEvent(AuthEvent::BEFORE_REGISTRATION, ['model' => $model])) {

                $user = $model->signup(false, $model);

                // Trigger event "after registration" and checks if it's valid
                if ($user && $this->triggerModuleEvent(AuthEvent::AFTER_REGISTRATION, ['model' => $model, 'user' => $user])) {

                    if (Yii::$app->art->emailConfirmationRequired) {
                        Yii::$app->session->setFlash('info', Yii::t('art/auth', 'Check your e-mail {email} for instructions to activate account', ['email' => '<b>' . $user->email . '</b>']));
                        //return $this->renderIsAjax('signup-confirmation', compact('user'));
                    } else {
                        // $user->assignRoles(Yii::$app->art->defaultRoles);

                        Yii::$app->user->login($user);

                        return $this->redirect(Yii::$app->user->returnUrl);
                    }
                }
            }
        }

        return $this->renderIsAjax('signup', compact('model'));
    }

    /**
     * Receive token after registration, find user by it and confirm email
     *
     * @param string $token
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionConfirmRegistrationEmail($token)
    {
        if (Yii::$app->art->emailConfirmationRequired) {

            $model = new SignupForm();
            $user = $model->checkConfirmationToken($token);

            if ($user) {
                return $this->renderIsAjax('confirm-email-success', compact('user'));
            }

            throw new NotFoundHttpException(Yii::t('art/auth', 'Token not found. It may be expired'));
        }
    }

    /**
     * Change your own password
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdatePassword()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if (!User::hasPermission('changeOwnPassword')) {
            throw new ForbiddenHttpException(Yii::t('art', 'You are not allowed to perform this action.'));
        }
        $user = User::getCurrentUser();

        if ($user->status != User::STATUS_ACTIVE) {
            throw new ForbiddenHttpException(Yii::t('art', 'You are not allowed to perform this action.'));
        }

        $model = new UpdatePasswordForm(compact('user'));

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->updatePassword(false)) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', Yii::t('art/auth', 'Password has been updated'));
            return $this->redirect('login');
            //return $this->renderIsAjax('update-password-success');
        }

        return $this->renderIsAjax('update-password', compact('model'));
    }

    /**
     * Action to reset password
     * @return bool|string|Response
     * @throws NotFoundHttpException
     */
    public function actionResetPassword()
    {
        if (!Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = new ResetPasswordForm();

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model->validate();
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if ($this->triggerModuleEvent(AuthEvent::BEFORE_PASSWORD_RECOVERY_REQUEST, ['model' => $model])) {
                if ($model->sendEmail(false)) {
                    if ($this->triggerModuleEvent(AuthEvent::AFTER_PASSWORD_RECOVERY_REQUEST, ['model' => $model])) {
                        return $this->renderIsAjax('reset-password-success');
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('art/auth', "Unable to send message for email provided"));
                }
            }
        }

        return $this->renderIsAjax('reset-password', compact('model'));
    }

    /**
     * Receive token, find user by it and show form to change password
     *
     * @param string $token
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionResetPasswordRequest($token)
    {
        if (!Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = User::findByConfirmationToken($token);

        if (!$user) {
            throw new NotFoundHttpException(Yii::t('art/auth', 'Token not found. It may be expired. Try reset password once more'));
        }

        $model = new UpdatePasswordForm([
            'scenario' => 'restoreViaEmail',
            'user' => $user,
        ]);

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if ($this->triggerModuleEvent(AuthEvent::BEFORE_PASSWORD_RECOVERY_COMPLETE, ['model' => $model])) {
                $model->updatePassword(false);
                if ($this->triggerModuleEvent(AuthEvent::AFTER_PASSWORD_RECOVERY_COMPLETE, ['model' => $model])) {
                    return $this->renderIsAjax('update-password-success');
                }
            }
        }

        return $this->renderIsAjax('update-password', compact('model'));
    }

    /**
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmEmail()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = User::getCurrentUser();

        if ($user->email_confirmed == 1) {
            return $this->renderIsAjax('confirmEmailSuccess', compact('user'));
        }

        $model = new ConfirmEmailForm([
            'email' => $user->email,
            'user' => $user,
        ]);

        if (Yii::$app->request->isAjax AND $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if ($this->triggerModuleEvent(AuthEvent::BEFORE_EMAIL_CONFIRMATION_REQUEST, ['model' => $model])) {
                if ($model->sendEmail(false)) {
                    if ($this->triggerModuleEvent(AuthEvent::AFTER_EMAIL_CONFIRMATION_REQUEST, ['model' => $model])) {
                        return $this->refresh();
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('art/auth', "Unable to send message for email provided"));
                }
            }
        }

        return $this->renderIsAjax('confirm-email', compact('model'));
    }

    /**
     * Receive token, find user by it and confirm email
     *
     * @param string $token
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionConfirmEmailReceive($token)
    {
        $user = User::findByConfirmationToken($token);

        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user->email_confirmed = 1;
        $user->removeConfirmationToken();
        $user->save();

        return $this->renderIsAjax('confirm-email-success', compact('user'));
    }

    /**
     * Universal method for triggering events like "before registration", "after registration" and so on
     *
     * @param string $eventName
     * @param array $data
     *
     * @return bool
     */
    protected function triggerModuleEvent($eventName, $data = [])
    {
        $event = new AuthEvent($data);

        Yii::$app->art->trigger($eventName, $event);

        return $event->isValid;
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionProfile()
    {
        if ($this->module->profileLayout) {
            $this->layout = $this->module->profileLayout;
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = ProfileForm::findIdentity(Yii::$app->user->id);
        $userCommon = $user->userCommon;
        if (!$userCommon) {
            Yii::$app->session->addFlash('error', 'Недостаточно данных для загрузки формы.');
            return $this->goHome();
        }
        $userCommon->scenario = UserCommon::SCENARIO_DEFAULT;
        if ($userCommon->load(Yii::$app->request->post()) && $user->load(Yii::$app->request->post())) {
            //echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>';
            // validate all models
            $valid = $userCommon->validate();
            $valid = $user->validate() && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($user->save(false) && $userCommon->save(false)) {
                        $transaction->commit();
                        return $this->redirect(['profile']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->renderIsAjax('profile', compact(['user', 'userCommon']));
    }

    /**
     * @return string|void
     * @throws NotFoundHttpException
     */
    public function actionUploadAvatar()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new DynamicModel(['image']);
        $model->addRule('image', 'file', ['skipOnEmpty' => false, 'extensions' => 'png, jpg']);

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstanceByName('image');

            if ($model->validate()) {
                try {
                    return AvatarHelper::saveAvatar($model->image);
                } catch (Exception $exc) {
                    Yii::$app->response->statusCode = 400;
                    return Yii::t('art', 'An unknown error occurred.');
                }
            } else {
                $errors = $model->getErrors();
                Yii::$app->response->statusCode = 400;
                return $model->getFirstError(key($errors));
            }
        }

        return;
    }

    /**
     * @return bool|string|void
     * @throws NotFoundHttpException
     */
    public function actionRemoveAvatar()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            Yii::$app->user->identity->removeAvatar();
            AvatarAsset::register($this->view);
            return AvatarAsset::getDefaultAvatar('large');
        } catch (Exception $exc) {
            Yii::$app->response->statusCode = 400;
            return 'Error occured!';
        }

        return;
    }

    /**
     * @param null $redirectUrl
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUnlinkOauth($redirectUrl = null)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $client = Yii::$app->getRequest()->get('authclient');
        if (!Auth::unlinkClient($client)) {
            Yii::$app->session->addFlash('error', 'Error cant unlink');
        }

        if ($redirectUrl === null) {
            $redirectUrl = ['/auth/default/profile'];
        }

        return $this->redirect($redirectUrl);
    }
}