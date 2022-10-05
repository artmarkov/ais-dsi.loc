<?php

namespace common\models\history;

use common\models\entrant\EntrantGroup;
use common\widgets\history\BaseHistory;

class EntrantGroupHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_group_hist';
    }

    public static function getModelName()
    {
        return EntrantGroup::class;
    }

    protected function getFields()
    {
        return [
            'name',
            'prep_flag',
            'timestamp_in',
            'description',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'prep_flag':
                return isset($model->prep_flag) ?  EntrantGroup::getPrepValue($model->prep_flag) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}