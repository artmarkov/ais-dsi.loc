<?php

namespace common\models\efficiency;

use Yii;
use common\models\teachers\Teachers;

/**
 * This is the model class for table "teachers_efficiency".
 *
 * @property int $id
 * @property int $efficiency_id
 * @property int $teachers_id
 * @property string|null $bonus
 * @property int $date_in
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideEfficiencyTree $efficiency
 * @property Teachers $teachers
 */
class TeachersEfficiency extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_efficiency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['efficiency_id', 'teachers_id', 'date_in', 'created_at', 'updated_at'], 'required'],
            [['efficiency_id', 'teachers_id', 'date_in', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['efficiency_id', 'teachers_id', 'date_in', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['bonus'], 'string', 'max' => 127],
            [['efficiency_id'], 'exist', 'skipOnError' => true, 'targetClass' => EfficiencyTree::className(), 'targetAttribute' => ['efficiency_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/efficiency', 'ID'),
            'efficiency_id' => Yii::t('art/efficiency', 'Efficiency ID'),
            'teachers_id' => Yii::t('art/efficiency', 'Teachers ID'),
            'bonus' => Yii::t('art/efficiency', 'Bonus'),
            'date_in' => Yii::t('art/efficiency', 'Date In'),
            'created_at' => Yii::t('art/efficiency', 'Created At'),
            'created_by' => Yii::t('art/efficiency', 'Created By'),
            'updated_at' => Yii::t('art/efficiency', 'Updated At'),
            'updated_by' => Yii::t('art/efficiency', 'Updated By'),
            'version' => Yii::t('art/efficiency', 'Version'),
        ];
    }

    /**
     * Gets query for [[Efficiency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEfficiency()
    {
        return $this->hasOne(EfficiencyTree::className(), ['id' => 'efficiency_id']);
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
