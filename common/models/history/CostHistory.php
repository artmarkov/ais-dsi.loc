<?php

namespace common\models\history;

use common\models\guidejob\Cost;
use common\widgets\history\BaseHistory;

class CostHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_cost_hist';
    }

    public static function getModelName()
    {
        return Cost::class;
    }

    protected function getFields()
    {
        return [
            'direction_id',
            'stake_id',
            'stake_value',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'direction_id':
                return isset($model->direction_id) ? $model->getDirectionName() : $value;
            case 'stake_id':
                return isset($model->stake_id) ? $model->getStakeName() : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}