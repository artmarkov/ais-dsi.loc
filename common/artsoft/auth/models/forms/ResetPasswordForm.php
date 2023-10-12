<?php

namespace artsoft\auth\models\forms;

use artsoft\models\User;
use Yii;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */

    public $username;
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'username', 'captcha'], 'required'],
            [['email', 'username'], 'trim'],
            [['username'], 'filter', 'filter' => 'strtolower'],
            ['email', 'email'],
            ['email', 'validateEmailConfirmedAndUserActive'],
            ['captcha', 'captcha', 'captchaAction' => '/auth/default/captcha'],
        ];
    }

    /**
     * @return bool
     */
    public function validateEmailConfirmedAndUserActive()
    {
        if (!Yii::$app->art->checkAttempts()) {
            $this->addError('email', Yii::t('art/auth', 'Too many attempts'));
            return false;
        }

        $user = User::findOne([
            'username' => $this->username,
            'email' => $this->email,
            'email_confirmed' => 1,
            'status' => User::STATUS_ACTIVE,
        ]);

        if ($user) {
            $this->user = $user;
        } else {
            $this->addError('email', Yii::t('art/auth', 'A Login and E-mail not found.'));
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'username' => Yii::t('art/auth', 'Login'),
            'captcha' => Yii::t('art', 'Captcha'),
        ];
    }

    /**
     * @param bool $performValidation
     *
     * @return bool
     */
    public function sendEmail($performValidation = true)
    {
        if ($performValidation AND !$this->validate()) {
            return false;
        }

        $this->user->generateConfirmationToken();
        $this->user->save(false);

        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['password-reset'],
            ['user' => $this->user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($this->email)
            ->setSubject(Yii::t('art/auth', 'Password reset for') . ' ' . Yii::$app->name)
            ->send();
    }
}