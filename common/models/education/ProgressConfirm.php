<?php

namespace common\models\education;

use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "progress_confirm".
 *
 * @property int $id
 * @property int $subject_sect_studyplan_id
 * @property int|null $timestamp_month Отчетный месяц-год - timestamp
 * @property int|null $teachers_id
 * @property int|null $teachers_sign
 * @property int $confirm_status
 * @property string|null $sign_message
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Teachers $teachers
 * @property Teachers $teachersSign
 */
class ProgressConfirm  extends \artsoft\db\ActiveRecord
{
    public $admin_flag;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'progress_confirm';
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
            [['subject_sect_studyplan_id', 'timestamp_month', 'teachers_id', 'teachers_sign'], 'required'],
            [['subject_sect_studyplan_id', 'timestamp_month', 'teachers_id', 'teachers_sign', 'confirm_status'], 'integer'],
            [['sign_message'], 'string', 'max' => 1024],
            [['confirm_status'], 'default', 'value' => self::DOC_STATUS_DRAFT],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
            [['teachers_sign'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_sign' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art', 'Subject Sect Studyplan ID'),
            'timestamp_month' => Yii::t('art', 'Timestamp Month'),
            'teachers_id' => Yii::t('art', 'Teachers ID'),
            'confirm_status' => Yii::t('art/guide', 'Doc Status'),
            'teachers_sign' => Yii::t('art/guide', 'Sign Teachers'),
            'sign_message' => 'Сообщение от администратора',
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
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::className(), ['id' => 'teachers_id']);
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
            self::DOC_STATUS_AGREED => Yii::t('art', 'Verified'),
            self::DOC_STATUS_WAIT => Yii::t('art', 'OnVerif'),
            self::DOC_STATUS_MODIF => Yii::t('art', 'Modif'),
        );
    }

    public static function getDocStatusListOptions()
    {
        return array(
            [self::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
            [self::DOC_STATUS_AGREED, Yii::t('art', 'Verified'), 'success'],
            [self::DOC_STATUS_WAIT, Yii::t('art', 'OnVerif'), 'warning'],
            [self::DOC_STATUS_MODIF, Yii::t('art', 'Modif'), 'warning'],
        );
    }

    public function modifMessage()
    {
        $receiverId =  RefBook::find('teachers_users')->getValue($this->teachers_id);
        Yii::$app->mailbox->send($receiverId, 'modif', $this, $this->sign_message);
    }

    public function approveMessage()
    {
        $receiverId =  RefBook::find('teachers_users')->getValue($this->teachers_id);
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

    public function getSubject() {
        return RefBook::find('sect_name_2')->getValue($this->subject_sect_studyplan_id);
    }

    public static function getLastSigner($teachers_id)
    {
        return self::find()->select(['teachers_sign'])
            ->where(['teachers_id' => $teachers_id])
            ->andWhere(['IS NOT', 'teachers_sign', null])
            ->orderBy('id DESC')
            ->scalar();
    }
}
