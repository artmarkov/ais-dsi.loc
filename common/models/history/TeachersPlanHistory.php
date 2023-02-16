<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\studyplan\Studyplan;
use common\models\teachers\TeachersPlan;
use common\widgets\history\BaseHistory;

class TeachersPlanHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_plan_hist';
    }

    public static function getModelName()
    {
        return TeachersPlan::class;
    }

    protected function getFields()
    {
        return [
            'direction_id',
            'teachers_id',
            'plan_year',
            'half_year',
            'week_num',
            'week_day',
            'time_plan_in',
            'time_plan_out',
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
            case 'direction_id':
                return isset($model->direction_id) ? $model->direction->name : $value;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
            case 'half_year':
                return isset($model->half_year) ? ArtHelper::getHalfYearValue($value) : $value;
            case 'plan_year':
                return isset($model->plan_year) ? ArtHelper::getStudyYearsValue($value) : $value;
            case 'week_num':
                return isset($model->week_num) ? ArtHelper::getWeekValue('name', $model->week_num) : $value;
            case 'week_day':
                return isset($model->week_day) ? ArtHelper::getWeekdayValue('name', $model->week_day) : $value;
            case 'auditory_id':
                return isset($model->auditory_id) ? RefBook::find('auditory_memo_1')->getValue($model->auditory_id) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

}