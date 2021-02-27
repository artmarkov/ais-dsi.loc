<?php

namespace common\models\subject;

use Yii;

/**
 * This is the model class for table "{{%subject_department}}".
 *
 * @property int $id
 * @property int $subject_id
 * @property int $department_id
 *
 * @property Department $department
 * @property Subject $subject
 */
class SubjectDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subject_department}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_id', 'department_id'], 'required'],
            [['subject_id', 'department_id'], 'integer'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_id' => Yii::t('art/guide', 'Subject ID'),
            'department_id' => Yii::t('art/guide', 'Department ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
}
