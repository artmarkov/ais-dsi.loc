<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\subject\SubjectType;
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

    public function behaviors()
    {
        return [
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['department_list', 'subject_type_list'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', 'status', 'department_list', 'subject_type_list'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['short_name'], 'string', 'max' => 64],
            [['department_list', 'subject_type_list'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'short_name' => Yii::t('art', 'Short Name'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'subject_type_list' => Yii::t('art/guide', 'Subject Type'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * Получаем все категории дисциплины из спецификации
     * @return array
     */
    public static function getTypeList($speciality_id)
    {
        $data = [];
        $subject_type_list = self::find()->select(['subject_type_list'])->where(['=', 'id', $speciality_id])->scalar();
        foreach (explode(',', $subject_type_list) as $item => $subject_type_id) {
            $data[$subject_type_id] = SubjectType::find()->select(['name'])->where(['=', 'id', $subject_type_id])->scalar();
        }
        return $data;
    }
}
