<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\efficiency\TeachersEfficiency;
use common\widgets\history\BaseHistory;

class EfficiencyHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_efficiency_hist';
    }

    public static function getModelName()
    {
        return TeachersEfficiency::class;
    }

    protected function getFields()
    {
        return [
            'efficiency_id',
            'teachers_id',
            'date_in',
            'bonus',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return null
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'efficiency_id':
                return isset($model->efficiency_id) ? $model->getEfficiencyName() : $value;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}