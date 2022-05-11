<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body', 'verifyCode'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('art', 'First Name'),
            'email' => Yii::t('art', 'Email'),
            'subject' => Yii::t('art', 'Subject'),
            'body' => Yii::t('art', 'Content'),
            'verifyCode' => Yii::t('art', 'Captcha'),
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->art->emailSender)
            ->setTo($email)
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }
//    public function sendEmail($email)
//    {
//        return Yii::$app->mailer->compose(
//            Yii::$app->art->emailTemplates['send-contact'],
//            [
//                'body' => $this->body,
//                'subject' => $this->subject,
//                'email' => $this->email,
//            ])
//            ->setFrom($email)
//            ->setTo($email)
//            ->setSubject(Yii::t('art', 'Message for') . ' ' . \artsoft\helpers\Html::encode(Yii::$app->settings->get('general.title', Yii::$app->name, Yii::$app->language)))
//            ->send();
//    }
}
