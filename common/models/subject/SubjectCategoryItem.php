<?php

namespace common\models\subject;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "{{%subject_category_item}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $sortOrder
 *
 * @property SubjectCategory[] $subjectCategories
 */
class SubjectCategoryItem extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subject_category_item}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sortOrder',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'status'], 'required'],
            [['slug', 'name'], 'unique'],
            [['sortOrder', 'status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 64],
            
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
            'sortOrder' => Yii::t('art/guide', 'Order'),
            'status' => Yii::t('art/guide', 'Status'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategories()
    {
        return $this->hasMany(SubjectCategory::className(), ['category_id' => 'id']);
    }
}
