<?php

namespace common\models\routine;

use Yii;

/**
 * This is the model class for table "{{%routine_cat}}".
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property int $plan_flag Учитывать при планировании
 *
 * @property Routine[] $routines
 */
class RoutineCat extends \yii\db\ActiveRecord
{
    const FLAG_ACTIVE = 1;
    const FLAG_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%routine_cat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'color', 'plan_flag'], 'required'],
            [['plan_flag'], 'integer'],
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
            'plan_flag' => Yii::t('art/routine', 'Plan Flag'),
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

    public static function getPlanFlagList() {
        return array(
            self::FLAG_ACTIVE => Yii::t('art', 'Yes'),
            self::FLAG_INACTIVE => Yii::t('art', 'No'),
        );
    }

    public static function getPlanFlagValue($val) {
        $ar = self::getPlanFlagList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public static function getCatList()
    {
        return \yii\helpers\ArrayHelper::map(RoutineCat::find()->all(), 'id', 'name');
    }
}
