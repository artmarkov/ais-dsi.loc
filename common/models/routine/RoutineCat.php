<?php

namespace common\models\routine;

use artsoft\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "guide_routine_cat".
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property int $vacation_flag
 * @property int $dayoff_flag
 *
 * @property Routine[] $routines
 */
class RoutineCat extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_routine_cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['vacation_flag', 'dayoff_flag'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 127],
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
            'color' => Yii::t('art/routine', 'Color'),
            'vacation_flag' => Yii::t('art/routine', 'Vacation'),
            'dayoff_flag' => Yii::t('art/routine', 'Day off'),
        ];
    }

    /**
     * Gets query for [[Routines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoutines()
    {
        return $this->hasMany(Routine::className(), ['cat_id' => 'id']);
    }

    public static function getCatList()
    {
        return \yii\helpers\ArrayHelper::map(RoutineCat::find()->all(), 'id', 'name');
    }
}
