<?php

namespace common\models\schoolplan;

use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "schoolplan_protocol_confirm".
 *
 * @property int $id
 * @property int $schoolplan_id
 * @property bool $confirm_status
 * @property int|null $teachers_sign
 * @property int|null $sign_message
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Schoolplan $schoolplan
 * @property Teachers $teachersSign
 */
class SchoolplanProtocolConfirm extends \artsoft\db\ActiveRecord
{

    public $admin_flag;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_protocol_confirm';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['schoolplan_id', 'teachers_sign'], 'required'],
            [['schoolplan_id', 'confirm_status', 'teachers_sign', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['admin_flag'], 'boolean'],
            [['sign_message'], 'string'],
            [['sign_message'], 'required', 'when' => function ($model) {
                return $model->admin_flag;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"subjectscheduleconfirm-admin_flag\"]').prop('checked');
                            }"],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::className(), 'targetAttribute' => ['schoolplan_id' => 'id']],
            [['teachers_sign'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_sign' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolplan_id' => Yii::t('art/guide', 'Schoolpal'),
            'confirm_status' => Yii::t('art/guide', 'Doc Status'),
            'teachers_sign' => Yii::t('art/guide', 'Sign Teachers'),
            'sign_message' => 'Сообщение секретарю комиссии',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Schoolplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplan()
    {
        return $this->hasOne(Schoolplan::className(), ['id' => 'schoolplan_id']);
    }

    /**
     * Gets query for [[TeachersSign]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersSign()
    {
        return $this->hasOne(Teachers::className(), ['id' => 'teachers_sign']);
    }

    /**
     * getDocStatusList
     * @return array
     */
    public static function getDocStatusList()
    {
        return array(
            self::DOC_STATUS_DRAFT => Yii::t('art', 'Draft'),
            self::DOC_STATUS_AGREED => Yii::t('art', 'Agreed'),
            self::DOC_STATUS_WAIT => Yii::t('art', 'Wait'),
            self::DOC_STATUS_MODIF => Yii::t('art', 'Modif'),
        );
    }

    public static function getDocStatusListOptions()
    {
        return array(
            [self::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
            [self::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
            [self::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
            [self::DOC_STATUS_MODIF, Yii::t('art', 'Modif'), 'warning'],
        );
    }


    public function modifMessage()
    {
        $receiverId = $this->schoolplan->protocol_secretary_id;
        Yii::$app->mailbox->send($receiverId, 'modif', $this, $this->sign_message);
    }

    public function approveMessage()
    {
        $receiverId = $this->schoolplan->protocol_secretary_id;
        Yii::$app->mailbox->send($receiverId, 'approve', $this, $this->sign_message);
    }

    public function sendApproveMessage()
    {
        $receiverId =  RefBook::find('teachers_users')->getValue($this->teachers_sign);
        Yii::$app->mailbox->send($receiverId, 'send_approve', $this, $this->sign_message);
    }
    public function afterFind()
    {
        $this->sign_message = '';
        parent::afterFind();
    }
}
