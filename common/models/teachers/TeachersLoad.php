<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "teachers_load".
 *
 * @property int $id
 * @property int|null $sect_id
 * @property int $direction_id
 * @property int $teachers_id
 * @property float|null $week_time
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideTeachersDirection $direction
 * @property SubjectSect $sect
 * @property Teachers $teachers
 */
class TeachersLoad extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sect_id', 'direction_id', 'teachers_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['sect_id', 'direction_id', 'teachers_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['direction_id', 'teachers_id', 'created_at', 'updated_at'], 'required'],
            [['week_time'], 'number'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideTeachersDirection::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['sect_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSect::className(), 'targetAttribute' => ['sect_id' => 'id']],
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
            'sect_id' => Yii::t('art/guide', 'Sect ID'),
            'direction_id' => Yii::t('art/guide', 'Direction ID'),
            'teachers_id' => Yii::t('art/guide', 'Teachers ID'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
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
     * Gets query for [[Sect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSect()
    {
        return $this->hasOne(SubjectSect::className(), ['id' => 'sect_id']);
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
