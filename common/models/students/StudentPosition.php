<?php

namespace common\models\students;

use Yii;

/**
 * This is the model class for table "guide_student_position".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property Student[] $students
 */
class StudentPosition extends \yii\db\ActiveRecord
{
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_student_position';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 32],
            ['status', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }
    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['position_id' => 'id']);
    }

    public static function getPositionList()
    {
        return \yii\helpers\ArrayHelper::map(StudentPosition::find()->all(), 'id', 'name');
    }
}
