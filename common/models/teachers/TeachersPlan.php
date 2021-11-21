<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "teachers_plan".
 *
 * @property int $id
 * @property int $direction_id
 * @property int $teachers_id
 * @property int|null $plan_year
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideTeachersDirection $direction
 * @property Teachers $teachers
 */
class TeachersPlan extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id', 'teachers_id', 'created_at', 'updated_at'], 'required'],
            [['direction_id', 'teachers_id', 'plan_year', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['direction_id', 'teachers_id', 'plan_year', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideTeachersDirection::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'direction_id' => Yii::t('art/guide', 'Direction ID'),
            'teachers_id' => Yii::t('art/guide', 'Teachers ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory ID'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(GuideTeachersDirection::className(), ['id' => 'direction_id']);
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
}
