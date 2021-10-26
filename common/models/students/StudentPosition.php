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
class StudentPosition extends \artsoft\db\ActiveRecord
{

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
            [['name', 'slug'], 'required'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['position_id' => 'id']);
    }

    public static function getPositionList()
    {
        return \yii\helpers\ArrayHelper::map(StudentPosition::find()->all(), 'id', 'name');
    }
}
