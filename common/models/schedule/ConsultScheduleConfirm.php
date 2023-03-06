<?php

namespace common\models\schedule;

use artsoft\behaviors\DateFieldBehavior;
use common\models\teachers\Teachers;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "consult_schedule_confirm".
 *
 * @property int $id
 * @property int $teachers_id
 * @property int $plan_year
 * @property bool $confirm_flag
 * @property int|null $teachers_sign
 * @property int|null $timestamp_sign
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Teachers $teachers
 * @property Teachers $teachersSign
 */
class ConsultScheduleConfirm extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_confirm';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['timestamp_sign'],
                'timeFormat' => 'd.m.Y H:i'
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teachers_id', 'plan_year'], 'required'],
            [['teachers_id', 'plan_year', 'teachers_sign', 'timestamp_sign', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['confirm_flag'], 'boolean'],
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
            'plan_year' =>  Yii::t('art/studyplan', 'Plan Year'),
            'confirm_flag' => 'Confirm Flag',
            'teachers_sign' => 'Teachers Sign',
            'timestamp_sign' => 'Timestamp Sign',
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
}
