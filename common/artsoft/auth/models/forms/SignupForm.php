<?php

namespace artsoft\auth\models\forms;

use artsoft\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

class SignupForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $repeat_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['id', 'required'], // делаем update по ключу
            [['username', 'email', 'password', 'repeat_password'], 'required'],
            [['username', 'email', 'password', 'repeat_password'], 'trim'],
            [['email'], 'email'],
//            ['username', 'purgeXSS'],
            ['username', 'string', 'max' => 50],
            /*['username', 'match', 'pattern' => Yii::$app->art->usernameRegexp, 'message' => Yii::t('art/auth', 'The username should contain only Latin letters, numbers and the following characters: "-" and "_".')],
            ['username', 'match', 'not' => true, 'pattern' => Yii::$app->art->usernameBlackRegexp, 'message' => Yii::t('art/auth', 'Username contains not allowed characters or words.')],
            */
            ['password', 'string', 'max' => 255],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
        ];

        return $rules;
    }

    /**
     * Remove possible XSS stuff
     *
     * @param $attribute
     */
    public function purgeXSS($attribute)
    {
        $this->$attribute = Html::encode($this->$attribute);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('art/auth', 'Login'),
            'email' => Yii::t('art/auth', 'E-mail'),
            'password' => Yii::t('art/auth', 'Password'),
            'repeat_password' => Yii::t('art/auth', 'Repeat password'),
        ];
    }

    /**
     * @param bool $performValidation
     *
     * @return bool|User
     */
    public function signup($performValidation = true, $model)
    {
        if ($performValidation AND !$this->validate()) {
            return false;
        }
        if (empty($model)) $user = new User();
        else  $user = User::findOne($model->id);

        $user->password = $this->password;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->generateAuthKey();

        if (Yii::$app->art->emailConfirmationRequired) {
            $user->status = User::STATUS_INACTIVE;
            $user->generateConfirmationToken();
            // $user->save(false);
        }

        if (!$user->save()) {
            $this->addError('username', Yii::t('art/auth', 'Login has been taken'));
        } else {
            if (!$this->sendConfirmationEmail($user)) {
                $this->addError('username', Yii::t('art/auth', 'Could not send confirmation email'));
            }
            return $user;
        }

        return FALSE;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function sendConfirmationEmail($user)
    {
        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['signup-confirmation'], ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'E-mail confirmation for') . ' ' . Yii::$app->name)
            ->send();
    }

    /**
     * Check received confirmation token and if user found - activate it, set username, roles and log him in
     *
     * @param string $token
     *
     * @return bool|User
     */
    public function checkConfirmationToken($token)
    {
        $user = User::findInactiveByConfirmationToken($token);
      //  echo '<pre>' . print_r($user, true) . '</pre>';
        if ($user) {
            
            $user->status = User::STATUS_ACTIVE;
            $user->email_confirmed = 1;
            $user->removeConfirmationToken();
            $user->save(false);
            //$user->assignRoles(Yii::$app->art->defaultRoles);
            Yii::$app->user->login($user);

            return $user;
        }

        return false;
    }
}