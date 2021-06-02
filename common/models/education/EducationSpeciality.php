<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "education_speciality".
 *
 * @property int $id
 * @property string|null $name
 * @property string $short_name
 * @property string|null $department_list
 * @property string|null $subject_type_list
 * @property int $status
 */
class EducationSpeciality extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_speciality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['short_name', 'status'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['short_name'], 'string', 'max' => 64],
            [['department_list', 'subject_type_list'], 'string', 'max' => 1024],
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
            'short_name' => Yii::t('art/guide', 'Short Name'),
            'department_list' => Yii::t('art/guide', 'Department List'),
            'subject_type_list' => Yii::t('art/guide', 'Subject Type List'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }
}
