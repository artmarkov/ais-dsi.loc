<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * SupportForm is the model behind the contact form.
 */
class SupportForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $phone_optional;
    public $subject;
    public $body;
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'subject', 'body'], 'required'],
            ['email', 'email'],
            ['file', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf'],
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
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'subject' => Yii::t('art', 'Subject'),
            'body' => Yii::t('art', 'Content'),
            'file' => Yii::t('art', 'Attachment'),
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
            ->setSubject($this->subject . ' '. $this->email)
            ->setTextBody($this->body . ' ' . $this->email)
            ->send();
    }
}
