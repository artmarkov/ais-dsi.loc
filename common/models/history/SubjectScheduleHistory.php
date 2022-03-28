<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\guidejob\Direction;
use common\models\subjectsect\SubjectSchedule;
use common\models\teachers\TeachersLoad;
use common\widgets\history\BaseHistory;

class SubjectScheduleHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'subject_schedule_hist';
    }

    public static function getModelName()
    {
        return SubjectSchedule::class;
    }

    protected function getFields()
    {
        return [
            'teachers_load_id',
            'week_num',
            'week_day',
            'auditory_id',
            'time_in',
            'time_out',
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
            case 'teachers_load_id':
                $teachers_id = $model->teachersLoad ? $model->teachersLoad->teachers_id : null;
                $direction_id = $model->teachersLoad ? $model->teachersLoad->direction_id : null;
                $m = Direction::findOne($direction_id);
                return isset($model->teachers_load_id) ? RefBook::find('teachers_fio')->getValue($teachers_id) . '-' . $m->slug : $value;
            case 'week_num':
                return isset($model->week_num) ? ArtHelper::getWeekValue('short', $model->week_num) : $value;
            case 'week_day':
                return isset($model->week_day) ? ArtHelper::getWeekdayValue('short', $model->week_day) : $value;
            case 'auditory_id':
                return isset($model->auditory_id) ? RefBook::find('auditory_memo_1')->getValue($model->auditory_id) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

}