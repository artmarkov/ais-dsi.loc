<?php

namespace artsoft\settings\models;

use yii\helpers\ArrayHelper;

class ModuleSettings extends BaseSettingsModel
{
    const GROUP = 'module';

    public $day_in;
    public $day_out;

    public $student_delta_time;
    public $study_plan_month_in;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in'], 'required'],
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in'], 'string'],
            ]);
    }

    public function attributeLabels()
    {
        return [
            'day_in' => 'День начала периода',
            'day_out' => 'День окончания периода',
            'student_delta_time' => 'Возможный допуск на отклонение от полного времени проведения занятия',
            'study_plan_month_in' => 'Месяц начала учебного года(расчетного периода)',
        ];
    }

}

