<?php

namespace common\models\guidejob;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_teachers_bonus".
 *
 * @property int $id
 * @property int $bonus_category_id
 * @property int $bonus_vid_id
 * @property string $name
 * @property string $slug
 * @property string $value_default
 * @property int $status 1-активна, 0-удалена
 *
 * @property TeachersBonusCategory $bonusCategory
 */
class Bonus extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_teachers_bonus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bonus_category_id', 'name', 'slug'], 'required'],
            [['bonus_category_id', 'bonus_vid_id', 'status'], 'integer'],
            [['name', 'value_default'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 32],
            [['bonus_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => BonusCategory::class, 'targetAttribute' => ['bonus_category_id' => 'id']],
            [['name','slug'], 'unique'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'bonus_category_id' => Yii::t('art/teachers', 'Bonus Category ID'),
            'name' => Yii::t('art/teachers', 'Name'),
            'slug' => Yii::t('art/teachers', 'Slug'),
            'bonus_vid_id' => Yii::t('art/guide', 'Bonus Vid'),
            'value_default' => Yii::t('art/teachers', 'Bonus Value'),
            'status' => Yii::t('art/teachers', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonusCategory()
    {
        return $this->hasOne(BonusCategory::class, ['id' => 'bonus_category_id']);
    }
    /* Геттер для названия */
    public function getBonusCategoryName()
    {
        return $this->bonusCategory->name;
    }
     /* Геттер для названия */
    public function getMeasureName()
    {
        return $this->measure->name;
    }
     /* Геттер для названия */
    public function getMeasureSlug()
    {
        return $this->measure->slug;
    }
    /* Геттер для value + measure sl*/
    public function getMeasureValueSlugName() {
        return $this->value_default . ' ' . $this->measureSlug;
    }

    public static function getBonusList()
    {
        return ArrayHelper::map(self::find()
            ->innerJoin('guide_teachers_bonus_category', 'guide_teachers_bonus_category.id = guide_teachers_bonus.bonus_category_id')
            ->andWhere(['guide_teachers_bonus.status' => self::STATUS_ACTIVE])
            ->select('guide_teachers_bonus.id as id, guide_teachers_bonus.name as name, guide_teachers_bonus_category.name as name_category')
            ->orderBy('guide_teachers_bonus.bonus_category_id')
            ->addOrderBy('guide_teachers_bonus.name')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }
}
