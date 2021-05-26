<?php

namespace common\models\history;

use common\models\teachers\TeachersActivity;
use common\widgets\history\BaseHistory;

class TeachersActivityHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_activity_hist';
    }

    public static function getModelName()
    {
        return TeachersActivity::class;
    }

    protected function getFields()
    {
        return [
            'direction_vid_id',
            'direction_id',
            'stake_id',
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
            case 'direction_vid_id':
                return isset($model->direction_vid_id) ? $model->directionVid->name : $value;
            case 'direction_id':
                return isset($model->direction_id) ? $model->direction->name : $value;
            case 'stake_id':
                return isset($model->stake_id) ? $model->stake->name : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}