<?php

namespace common\models\creative;

use Yii;

/**
 * This is the model class for table "creative_works_department".
 *
 * @property int $id
 * @property int $works_id
 * @property int $department_id
 *
 * @property CreativeWorks $works
 * @property Department $department
 */
class CreativeWorksDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'creative_works_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['works_id', 'department_id'], 'required'],
            [['works_id', 'department_id'], 'integer'],
            [['works_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreativeWorks::className(), 'targetAttribute' => ['works_id' => 'id']],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/creative', 'ID'),
            'works_id' => Yii::t('art/creative', 'Works ID'),
            'department_id' => Yii::t('art/creative', 'Department ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorks()
    {
        return $this->hasOne(CreativeWorks::className(), ['id' => 'works_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }
}
