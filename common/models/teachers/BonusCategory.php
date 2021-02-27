<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "{{%teachers_bonus_category}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $multiple
 *
 * @property TeachersBonusItem[] $teachersBonusItems
 */
class BonusCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teachers_bonus_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
           /* ['multiple', 'required'],
            [['multiple'], 'integer'],*/
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 127],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'name' => Yii::t('art/teachers', 'Name'),
            'slug' => Yii::t('art/teachers', 'Slug'),
           // 'multiple' => Yii::t('art/teachers', 'Multiple'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonusItems()
    {
        return $this->hasMany(BonusItem::className(), ['bonus_category_id' => 'id']);
    }

    public static function getBonusCategoryList()
    {
        return \yii\helpers\ArrayHelper::map(BonusCategory::find()->all(), 'id', 'name');

    }

}
