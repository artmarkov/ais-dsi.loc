<?php

namespace common\models\schedule;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\teachers\Teachers;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject_schedule_confirm".
 *
 * @property int $id
 * @property int $teachers_id
 * @property int $plan_year
 * @property bool $confirm_status
 * @property int|null $teachers_sign
 * @property int|null $sign_message
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Teachers $teachers
 * @property Teachers $teachersSign
 */
class SubjectScheduleConfirm extends \artsoft\db\ActiveRecord
{

    public $admin_flag;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_confirm';
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
            [['teachers_id', 'teachers_sign', 'plan_year'], 'required'],
            [['teachers_id', 'plan_year', 'confirm_status', 'teachers_sign', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['admin_flag'], 'boolean'],
            [['sign_message'], 'string'],
            [['sign_message'], 'required', 'when' => function ($model) {
                return $model->admin_flag;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"subjectscheduleconfirm-admin_flag\"]').prop('checked');
                            }"],
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
            'id' => 'ID',
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'confirm_status' => Yii::t('art/guide', 'Doc Status'),
            'teachers_sign' => Yii::t('art/guide', 'Sign Teachers'),
            'sign_message' => Yii::t('art/guide', 'Sign Message'),
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

    /**
     * @return |null
     */
    public static function getAuthorId()
    {
        $userId = Yii::$app->user->identity->getId();
        return RefBook::find('users_teachers')->getValue($userId) ?? null;
    }

    public function getAuthorEmail()
    {
        $id = RefBook::find('teachers_users')->getValue($this->teachers_id) ?? null;
        $user = User::findOne($id);
        return $user->email ?? false;
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        return $this->teachers_id == self::getAuthorId();
    }

//    public function sendAdminMessage()
//    {
//
//        if ($this->sign_message != '') {
//            $textBody = 'Сообщение модуля "Расписание занятий" ' . PHP_EOL;
//            $htmlBody = '<p><b>Сообщение модуля "Расписание занятий"</b></p>';
//
//            $textBody .= 'Прошу Вас внести уточнения в Расписание занятий на: ' . strip_tags(ArtHelper::getStudyYearsValue($this->plan_year)) . ' учебный год. ' . PHP_EOL;
//            $htmlBody .= '<p>Прошу Вас внести уточнения в Расписание занятий на:' . strip_tags(ArtHelper::getStudyYearsValue($this->plan_year)) . ' учебный год. ' . '</p>';
//            $textBody .= $this->sign_message . PHP_EOL;
//            $htmlBody .= '<p>' . $this->sign_message . '</p>';
//            $textBody .= '--------------------------' . PHP_EOL;
//            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
//            $htmlBody .= '<hr>';
//            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';
//
//            return Yii::$app->mailqueue->compose()
//                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
//                ->setTo($this->getAuthorEmail() ?? Yii::$app->params['adminEmail'])
//                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
//                ->setTextBody($textBody)
//                ->setHtmlBody($htmlBody)
//                ->queue();
//        }
//    }

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
}
