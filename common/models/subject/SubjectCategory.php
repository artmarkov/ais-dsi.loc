<?php

namespace common\models\subject;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_subject_category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $sort_order
 * @property int $dep_flag
 * @property int $frequency
 *
 * @property SubjectCategory[] $subjectCategories
 */
class SubjectCategory extends \artsoft\db\ActiveRecord
{

    const WEEKLY  = 0;
    const MONTHLY = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_subject_category';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'sort_order',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'status', 'frequency'], 'required'],
            [['slug', 'name'], 'unique'],
            [['sort_order', 'status', 'dep_flag', 'frequency'], 'integer'],
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
            'dep_flag' => Yii::t('art/guide', 'Department Dependence'),
            'sort_order' => Yii::t('art/guide', 'Order'),
            'frequency' => Yii::t('art/guide', 'Frequency'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategories()
    {
        return $this->hasMany(SubjectCategory::class, ['category_id' => 'id']);
    }

    /**
     * getFraquencyList
     * @return array
     */
    public static function getFraquencyList()
    {
        return array(
            self::WEEKLY  => Yii::t('art/guide', 'Weekly'),
            self::MONTHLY => Yii::t('art/guide', 'Monthly'),
        );
    }

    /**
     * проверка на периодичность занятия (ежемесячно)
     *
     * @return \yii\db\ActiveQuery
     */
    public function isMonthly()
    {
        return $this->frequency === self::MONTHLY ? true : false;
    }

    /**
     * проверка на периодичность занятия (еженедельно)
     *
     * @return \yii\db\ActiveQuery
     */
    public function isWeekly()
    {
        return $this->frequency === self::WEEKLY ? true : false;
    }

    /**
     * @return array
     */
    public static function getCategoryList()
    {
        return ArrayHelper::map(self::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->select('id, name')
            ->orderBy('sort_order')
            ->asArray()->all(), 'id', 'name');
    }
}
