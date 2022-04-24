<?php

namespace artsoft\mailbox\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use artsoft\fileinput\models\FileManager;
use artsoft\mailbox\traits\DateTimeTrait;

/**
 * This is the model class for table "mailbox".
 *
 * @property int $id
 * @property int $sender_id
 * @property string $title
 * @property string $content
 * @property int $status_post
 * @property int $status_del
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 *
 * @property User $sender
 * @property MailboxInbox[] $MailboxInboxs
 */
class Mailbox extends \artsoft\db\ActiveRecord
{
    
    use DateTimeTrait;
    
    public $gridReceiverSearch;
    public $statusDelTrash;

    const SCENARIO_COMPOSE = 'compose';
        
    const STATUS_POST_DRAFT = 1;    // черновик
    const STATUS_POST_SENT = 2;     // отправленные 
    
    const STATUS_DEL_NO = 0;        // не удалено   
    const STATUS_DEL_TRASH = 1;     // в корзине   
    const STATUS_DEL_DELETE = -1;   // удалено в скрытую папку    
    
    const STATUS_READ_NEW = 0;      // не прочитано
    const STATUS_READ_OLD = 1;      // прочитано 
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mailbox';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),            
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'sender_id',
                'updatedByAttribute' => NULL,
            ],
            [
                'class' => \artsoft\behaviors\ManyHasManyBehavior::className(),
                'relations' => [
                    'receivers' => 'receivers_ids',
                ],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::className(),               
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [            
            ['receivers_ids', 'required', 'on' => self::SCENARIO_COMPOSE, 'enableClientValidation' => false],
            [['status_post', 'status_del', 'deleted_at'], 'integer'],
            [['sender_id', 'created_at', 'updated_at'], 'safe'],
            ['receivers_ids', 'each', 'rule' => ['integer']],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'sender_id' => Yii::t('art/mailbox', 'Sender ID'),
            'receivers_ids' => Yii::t('art/mailbox', 'Receivers'),
            'title' => Yii::t('art', 'Title'),
            'content' => Yii::t('art', 'Content'),
            'status_post' => Yii::t('art/mailbox', 'Status Post'),
            'status_del' => Yii::t('art/mailbox', 'Status Del'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'deleted_at' => Yii::t('art/mailbox', 'Deleted At'),
            'gridReceiverSearch' => Yii::t('art/mailbox', 'Receivers'),
        ];
    }
    
    /**
     * {@inheritdoc}
     * @return MailboxQuery the active query used by this AR class.
     */
    public static function find() {
        return new MailboxQuery(get_called_class());
    }

    /**
     * 
     * @param type $status_post
     * @return $this
     */
    public function getComposeData($status_post) {
        $this->status_post = $status_post;

        switch ($status_post) {
            case self::STATUS_POST_SENT : {
                    $this->scenario = self::SCENARIO_COMPOSE;
                } break;
            case self::STATUS_POST_DRAFT : {
                    $this->deleted_at = NULL;
                } break;
            default: break;
        }

        return $this;
    }

    /**
     * @param type $model
     * @return $this
     */
    public function getReplyData($model) {

        $this->title = "Re:" . $model->title;
        $this->content = $this->getReplyContent($model);
        $this->receivers_ids = [
            $model->sender_id
        ];
        $this->status_post = self::STATUS_POST_DRAFT;
        return $this;
    }

    /**
     * 
     * @param type $id_old
     * @param type $id_new
     */
    public function copyForwardFiles($id_old, $id_new) {
        $column = FileManager::find()
                        ->andWhere(['class' => $this->formName()])
                        ->andWhere(['item_id' => $id_old])
                        ->asArray()->column();
        foreach ($column as $id) {
            $model_old = FileManager::findOne($id);
            $model_new = new FileManager;
            $model_new->attributes = $model_old->attributes;
            $model_new->item_id = $id_new;
            $model_new->save();
        }
    }

    /* 
     * @param type $model
     * @return $this
     */
    public function getForwardData($model) {

        $this->title = "Fwd:" . $model->title;
        $this->content = $this->getReplyContent($model);
        $this->status_post = self::STATUS_POST_DRAFT;

        return $this;
    }

    /**
     * @param type $model
     * @return type string
    */
    public function getReplyContent($model)
    {
        return "<blockquote>" . $model->createdDatetime . "&nbsp;from&nbsp;" . $model->senderName . ":<br><br>" . $model->content . "</blockquote>";
       
    }

    /**
     * @param type $status
     * @return type string
     */
    public static function getMessage($status){
       switch ($status) {
            case self::STATUS_POST_SENT :
                return Yii::t('art/mailbox', 'Your mail has been posted.');
            case self::STATUS_POST_DRAFT :
                return Yii::t('art/mailbox', 'Your mail has been moved to the drafts folder.');
            default:
                return NULL;
        } 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceivers() {
        return $this->hasMany(User::className(), ['id' => 'receiver_id'])
                        ->viaTable('mailbox_inbox', ['mailbox_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender() {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    /* Геттер для имени отправителя */

    public function getSenderName() {
        return $this->sender->username;
    }

    public function getMailboxFolder() {
        return $this->folder;
    }

    public function getShortContent($length = 64, $allowableTags = '') {
        $content = strip_tags($this->content, $allowableTags);
        return HtmlPurifier::process(mb_substr(Html::encode($content), 0, $length, "UTF-8")) . ((strlen($content) > $length) ? '...' : '');
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList() {
        return array(
            self::STATUS_READ_NEW => Yii::t('art/mailbox', 'New'),
            self::STATUS_READ_OLD => Yii::t('art/mailbox', 'Read'),
        );
    }

    /**
     * getStatusOptionsList
     * @return array
     */
     public static function getStatusOptionsList() {
        return [
            [self::STATUS_READ_NEW, Yii::t('art/mailbox', 'New'), 'success'],
            [self::STATUS_READ_OLD, Yii::t('art/mailbox', 'Read'), 'default']
        ];
    }  

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function trashMail($id) {
        $ret = false;

        $model = self::findOne($id);
        $model->status_del = self::STATUS_DEL_TRASH;
        $model->deleted_at = time();

        if ($model->save()) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function restoryMail($id) {
        $ret = false;

        $model = self::findOne([
                    'id' => $id,
                    'sender_id' => Yii::$app->user->identity->id
        ]);

        $modelVia = MailboxInbox::findOne([
                    'mailbox_id' => $id,
                    'receiver_id' => Yii::$app->user->identity->id
        ]);

        if ($model) {
            $model->status_del = self::STATUS_DEL_NO;
            $model->deleted_at = NULL;
            if ($model->save()) {
                $ret = true;
            }
        }
        if ($modelVia) {
            $modelVia->status_del = self::STATUS_DEL_NO;
            $modelVia->deleted_at = NULL;
            if ($modelVia->save()) {
                $ret = true;
            }
        }
        return $ret;
    }
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function deleteMail($id) {
        $ret = false;

        $model = self::findOne([
                    'id' => $id,
                    'sender_id' => Yii::$app->user->identity->id,
                    'status_del' => self::STATUS_DEL_TRASH
        ]);
        $modelVia = MailboxInbox::findOne([
                    'mailbox_id' => $id,
                    'receiver_id' => Yii::$app->user->identity->id,
                    'status_del' => self::STATUS_DEL_TRASH
        ]);
        if ($model) {
            $model->status_del = self::STATUS_DEL_DELETE;
            $model->deleted_at = time();
            if ($model->save()) {
                $ret = true;
            }
        }
        if ($modelVia) {
            $modelVia->status_del = self::STATUS_DEL_DELETE;
            $modelVia->deleted_at = time();
            if ($modelVia->save()) {
                $ret = true;
            }
        }
        return $ret;
    }
    
    /**
     * @param type $id
     * @return type array int
     */
    public static function getTrashOwnMail() {

        return self::find()->joinWith(['receivers'])
                        ->where(['OR', ['=', 'mailbox.sender_id', Yii::$app->user->identity->id], ['=', 'mailbox_inbox.receiver_id', Yii::$app->user->identity->id]])
                        ->andWhere(['OR', ['=', 'mailbox.status_del', self::STATUS_DEL_TRASH], ['=', 'mailbox_inbox.status_del', self::STATUS_DEL_TRASH]])
                        ->asArray()->column();
    } 
    /**
     * @param type $id
     * @return type array int
     */
    public static function getTrashMail() {

        return self::find()->joinWith(['receivers'])
                        ->where(['OR', ['=', 'mailbox.status_del', self::STATUS_DEL_TRASH], ['=', 'mailbox_inbox.status_del', self::STATUS_DEL_TRASH]])
                        ->asArray()->column();
    } 
    
    /**
     * @return type array int
     */
    public static function getDeletedMail() {
        return self::find()->where(['status_del' => self::STATUS_DEL_DELETE])->asArray()->column();
    }

    /**
     * 
     * @param type array $data
     * @return type bool
     */
    public static function clianDeletedMail($data) {
        $ret = false;

        foreach ($data as $id) {
            $count_all = MailboxInbox::find()
                            ->where([
                                'mailbox_id' => $id,
                            ])->count();

            $count_del = MailboxInbox::find()
                            ->where([
                                'mailbox_id' => $id,
                                'status_del' => self::STATUS_DEL_DELETE,
                            ])->count();

            if ($count_all == $count_del) {
                $model = self::findOne($id);
                $model->delete() ? $ret = true : false;
            }
        }
        return $ret;
    }

    /**
     * 
     * @param type int $id
     * @return type int
     */
    public static function getNextMail($id) {
        return self::find()->where(['>', 'id', $id])->andWhere([
                    'sender_id' => Yii::$app->user->identity->id,
                    'status_post' => self::STATUS_POST_SENT,
                    'status_del' => self::STATUS_DEL_NO,
                ])->min('id');
    }

    /**
     * 
     * @param type $id
     * @return type int
     */
    public static function getPrevMail($id) {
        return self::find()->where(['<', 'id', $id])->andWhere([
                    'sender_id' => Yii::$app->user->identity->id,
                    'status_post' => self::STATUS_POST_SENT,
                    'status_del' => self::STATUS_DEL_NO,
                ])->max('id');
    }

    /**
     * 
     * @return type string
     */
    public function getClip() {
        return ($this->filesCount > 0) ? '<i class="fa fa-paperclip" aria-hidden="true"></i>' : '';
    }
}
