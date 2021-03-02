<?php

namespace common\models\routine;

use Yii;
use tecnocen\yearcalendar\data\DataItem;
use tecnocen\yearcalendar\data\JsExpressionHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%routine}}".
 *
 * @property int $id
 * @property string $name
 * @property int $cat_id
 * @property int $start_date
 * @property int $end_date
 *
 * @property RoutineCat $cat
 */
class Routine extends ActiveRecord implements DataItem
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%routine}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cat_id', 'start_date', 'end_date'], 'required'],
            [['cat_id', 'start_date', 'end_date'], 'integer'],
            [['name','color'], 'string', 'max' => 1024],
            [['color'], 'string', 'max' => 127],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoutineCat::className(), 'targetAttribute' => ['cat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/routine', 'ID'),
            'name' => Yii::t('art/routine', 'Name'),
            'cat_id' => Yii::t('art/routine', 'Cat ID'),
            'start_date' => Yii::t('art/routine', 'Start Date'),
            'end_date' => Yii::t('art/routine', 'End Date'),
            'color' => Yii::t('art/routine', 'Color'),
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(RoutineCat::className(), ['id' => 'cat_id']);
    }

    public function getName()
    {
      return  $this->name;
    }

    public function getStartDate()
    {
        return JsExpressionHelper::parse($this->start_date);
    }

    public function getEndDate()
    {
        return JsExpressionHelper::parse($this->end_date);
    }
}
