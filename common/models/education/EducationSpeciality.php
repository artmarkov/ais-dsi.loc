<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
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
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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
}
