<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 13.09.2018
 * Time: 12:16
 */

namespace artsoft\auth\models\forms;

use Yii;

use common\models\user\User;

class ProfileForm extends User
{

    public function rules()
    {
        return [
            [['username', 'email', 'birth_timestamp'], 'required'],
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'middle_name', 'last_name', 'email'], 'trim'],
            ['email', 'email'],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            ['phone', 'required'],
            ['birth_timestamp', 'safe'],
            ['birth_timestamp', 'date', 'timestampAttribute' => 'birth_timestamp', 'format' => 'dd-MM-yyyy'],
            ['birth_timestamp', 'default', 'value' =>  mktime(0,0,0, date("m", time()), date("d", time()), date("Y", time()))],
            ['info', 'string', 'max' => 1024],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['email'])) {
            if (Yii::$app->art->emailConfirmationRequired) {
                $this->getCurrentUser();
                $this->generateConfirmationToken();
                $this->email_confirmed = 0;
                $this->status = User::STATUS_INACTIVE;
//                 echo '<pre>' . print_r($this, true) . '</pre>';

                if (!$this->save()) {
                    $this->addError('username', Yii::t('art/auth', 'Login has been taken'));
                } else {
                    if (!$this->sendConfirmationEmail($this)) {
                        $this->addError('email', Yii::t('art/auth', 'Could not send confirmation email'));
                    } else {
                        Yii::$app->session->setFlash('success', Yii::t('art/auth', 'Check your e-mail {email} for further instructions', ['email' => '<b>' . $this->email . '</b>']));
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
        }
    }

    protected function sendConfirmationEmail($user)
    {
        return Yii::$app->mailer->compose(Yii::$app->art->emailTemplates['profile-email-confirmation'], ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'E-mail confirmation for') . ' ' . Yii::$app->name)
            ->send();
    }
}