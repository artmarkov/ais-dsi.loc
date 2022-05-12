<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

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
            [['name', 'email', 'phone', 'subject', 'body', 'phone_optional'], 'string'],
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
     * @param string $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail($email)
    {
        $this->file = UploadedFile::getInstance($this, 'file');

        $sender = Yii::$app->mailer->compose(
            Yii::$app->art->emailTemplates['send-support'],
            [
                'email' => $this->email,
                'phone' => $this->phone,
                'phone_optional' => $this->phone_optional,
                'subject' => $this->subject,
                'body' => $this->body,
            ]);
        if ($this->file) {
            $this->file->saveAs(Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $this->file->name);
            $sender->attach(Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . $this->file->name);
        }
        $sender->setFrom(Yii::$app->art->emailSender)
            ->setTo($email)
            ->setSubject(Yii::t('art/mail', 'Message to the technical service') . ' ' . \artsoft\helpers\Html::encode(Yii::$app->settings->get('general.title', Yii::$app->name, Yii::$app->language)))
            ->send();

        return $sender;
    }
}
