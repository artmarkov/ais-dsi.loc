<?php

namespace common\models\history;

use common\models\auditory\Auditory;
use common\widgets\history\BaseHistory;

class AuditoryHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'auditory_hist';
    }

    public static function getModelName()
    {
        return Auditory::class;
    }

    protected function getFields()
    {
        return [
            'building_id',
            'cat_id',
            'num',
            'name',
            'floor',
            'area',
            'capacity',
            'description',
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
            case 'building_id':
                return isset($model->building_id) ? $model->getCatName() : $value;
            case 'cat_id':
                return isset($model->cat_id) ? $model->getBuildingName() : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}