<?php

namespace common\models\service;

use Yii;

/**
 * This is the model class for table "measure_unit".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property TeachersBonusItem[] $teachersBonusItems
 */
class MeasureUnit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'measure_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 16],
            [['slug'], 'string', 'max' => 8],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersBonusItems()
    {
        return $this->hasMany(TeachersBonusItem::className(), ['measure_id' => 'id']);
    }
    
    public static function getMeasureUnitList()
    {
        return \yii\helpers\ArrayHelper::map(MeasureUnit::find()->all(), 'id', 'name');

    }
}
