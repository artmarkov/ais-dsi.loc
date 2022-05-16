<?php
namespace artsoft\auth\models\forms;

use artsoft\models\User;
use Yii;

class ProfileForm extends User
{

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['email'])) {
            if (Yii::$app->art->emailConfirmationRequired) {
                $this->getCurrentUser();
                $this->generateConfirmationToken();
                $this->email_confirmed = 0;
                $this->status = User::STATUS_INACTIVE;

                if (!$this->save()) {
                    $this->addError('username', Yii::t('art/auth', 'Login has been taken'));
                } else {
                    if (!$this->sendConfirmationEmail($this)) {
                        $this->addError('email', Yii::t('art/auth', 'Could not send confirmation email'));
                    } else {
                        Yii::$app->session->setFlash('success', Yii::t('art/auth', 'Check your E-mail {email} for further instructions', ['email' => '<b>' . $this->email . '</b>']));
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
        }
    }

    protected function sendConfirmationEmail($user)
    {
        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['profile-email-confirmation'], ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'E-mail confirmation for') . ' ' . Yii::$app->name)
            ->send();
    }
}