<?php
namespace common\models\calendar;

use tecnocen\yearcalendar\data\DataItem;
use tecnocen\yearcalendar\data\JsExpressionHelper;
use yii\db\ActiveRecord;

class Conference extends ActiveRecord implements DataItem
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%conference}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['start_date', 'end_date'], 'required'],
        ];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStartDate()
    {
        return JsExpressionHelper::parse($this->start_date);
    }

    public function getEndDate()
    {
        return JsExpressionHelper::parse($this->end_date);
    }

    // rest of the active record code.
}