<?php

namespace artsoft\mailbox\models;

use Yii;
use artsoft\models\User;

/**
 * This is the model class for table "mailbox_inbox".
 *
 * @property int $id
 * @property int $mailbox_id
 * @property int $receiver_id
 * @property int $status_read
 * @property int $status_del
 * @property int $deleted_at
 *
 * @property User $receiver
 * @property Mailbox $mailbox
 */
class MailboxInbox extends \artsoft\db\ActiveRecord
{       
    public $mailboxSenderId;    
    public $mailboxTitle;    
    public $mailboxContent;    
    public $mailboxcreatedDate; 
    public $mailboxStatusPost; 
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mailbox_inbox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mailbox_id', 'receiver_id'], 'required'],
            [['mailbox_id', 'receiver_id', 'status_read', 'status_del', 'deleted_at'], 'integer'],
            [['receiver_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['receiver_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'mailbox_id' => Yii::t('art/mailbox', 'Mailbox ID'),
            'receiver_id' => Yii::t('art/mailbox', 'Receiver ID'),
            'status_read' => Yii::t('art/mailbox', 'Status Read'),
            'status_del' => Yii::t('art/mailbox', 'Status Del'),
            'deleted_at' => Yii::t('art/mailbox', 'Remoted At'),
        ];
    }

   /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(User::className(), ['id' => 'receiver_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailbox()
    {
        return $this->hasOne(Mailbox::className(), ['id' => 'mailbox_id']);
    }
 

    /**
     * {@inheritdoc}
     * @return MailboxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailboxQuery(get_called_class());
    }
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function trashMail($id) {
        $ret = false;

        $model = self::findOne($id);
        $model->status_del = Mailbox::STATUS_DEL_TRASH;
        $model->deleted_at = time();

        if ($model->save()) {
            $ret = true;
        }
        return $ret;
    }
    /**
     * Count new mail
     * @return type int
     */
    public static function getCountNewMail()
    {
        return self::find()
                ->joinWith(['mailbox'])
                ->where([
                    'receiver_id' => Yii::$app->user->identity->id,
                    'status_read' => Mailbox::STATUS_READ_NEW,
                    'status_post' => Mailbox::STATUS_POST_SENT,
                    'mailbox_inbox.status_del' => Mailbox::STATUS_DEL_NO,
                ])->count();
    }

    public static function getLabelNewMail()
    {
        $count = self::getCountNewMail();
        if ($count != 0)
        {
            return '<span class="label label-success pull-right">' . $count . '</span>';
        }
    }
    /**
     * 
     * @param type $id
     * @return type int
     */
    public static function getNextMail($id) {
        return self::find()
                ->joinWith(['mailbox'])
                ->where(['>', 'mailbox_inbox.id', $id])
                ->andWhere([
                    'receiver_id' => Yii::$app->user->identity->id,
                    'mailbox.status_post' => Mailbox::STATUS_POST_SENT,
                    'mailbox_inbox.status_del' => Mailbox::STATUS_DEL_NO,
                ])->min('mailbox_inbox.id');
    }

    /**
     * 
     * @param type $id
     * @return type int
     */
    public static function getPrevMail($id) {
        return self::find()
                ->joinWith(['mailbox'])
                ->where(['<', 'mailbox_inbox.id', $id])
                ->andWhere([
                    'receiver_id' => Yii::$app->user->identity->id,
                    'mailbox.status_post' => Mailbox::STATUS_POST_SENT,
                    'mailbox_inbox.status_del' => Mailbox::STATUS_DEL_NO,
                ])->max('mailbox_inbox.id');
    }

}
 