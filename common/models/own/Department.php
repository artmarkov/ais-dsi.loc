<?php

namespace common\models\own;

use Yii;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property int $id
 * @property int $division_id
 * @property string $name
 * @property string $slug
 * @property int $status
 *
 * @property TeachersDepartment[] $teachersDepartments
 */
class Department extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%department}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_id', 'status', 'name', 'slug'], 'required'],
            [['division_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 32],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['name','slug'], 'unique'],
        ];
    }
    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList() {
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
    public static function getStatusValue($val) {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'division_id' => Yii::t('art/guide', 'Division ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersDepartments()
    {
        return $this->hasMany(TeachersDepartment::className(), ['department_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }
    /* Геттер для названия */
    public function getDivisionName()
    {
        return $this->division->name;
    }
}
