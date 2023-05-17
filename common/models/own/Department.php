<?php

namespace common\models\own;

use artsoft\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_department".
 *
 * @property int $id
 * @property int $division_id
 * @property string $name
 * @property string $slug
 * @property int $status
 *
 * @property TeachersDepartment[] $teachersDepartments
 */
class Department extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_department';
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

    public static function getDepartmentList()
    {
        return ArrayHelper::map(self::find()
            ->innerJoin('guide_division', 'guide_division.id = guide_department.division_id')
            ->andWhere(['guide_department.status' => self::STATUS_ACTIVE])
            ->select('guide_department.id as id, guide_department.name as name, guide_division.name as name_category')
            ->orderBy('guide_division.id')
            ->addOrderBy('guide_department.name')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }

    public static function getDepartmentListById($division_id)
    {
        return self::find()
            ->innerJoin('guide_division', 'guide_division.id = guide_department.division_id')
            ->andWhere(['guide_department.status' => self::STATUS_ACTIVE])
            ->andWhere(['guide_department.division_id' => $division_id])
            ->select('guide_department.id as id, guide_department.name as name')
            ->orderBy('guide_department.name')
            ->asArray()->all();
    }

    public static function getDepartmentListByDivision($division_id)
    {
        return ArrayHelper::map(self::getDepartmentListById($division_id), 'id', 'name');
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
