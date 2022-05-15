<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 13.09.2018
 * Time: 12:16
 */

namespace artsoft\auth\models\forms;

use common\models\user\UserCommon;
use artsoft\models\User;
use Yii;

class ProfileForm extends UserCommon
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
        return Yii::$app->mailqueue->compose(Yii::$app->art->emailTemplates['profile-email-confirmation'], ['user' => $user])
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($user->email)
            ->setSubject(Yii::t('art/auth', 'E-mail confirmation for') . ' ' . Yii::$app->name)
            ->send();
    }
}