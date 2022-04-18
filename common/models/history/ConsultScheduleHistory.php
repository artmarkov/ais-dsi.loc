<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\schedule\ConsultSchedule;
use common\widgets\history\BaseHistory;

class ConsultScheduleHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'consult_schedule_hist';
    }

    public static function getModelName()
    {
        return ConsultSchedule::class;
    }

    protected function getFields()
    {
        return [
            'datetime_in',
            'datetime_out',
            'auditory_id',
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
            case 'auditory_id':
                return isset($model->auditory_id) ? RefBook::find('auditory_memo_1')->getValue($model->auditory_id) : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }
}