<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "teachers_department".
 *
 * @property int $id
 * @property int $teachers_id
 * @property int $department_id
 *
 * @property Department $department
 */
class TeachersDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teachers_id', 'department_id'], 'required'],
            [['teachers_id', 'department_id'], 'integer'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers ID'),
            'department_id' => Yii::t('art/teachers', 'Department ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }
}
