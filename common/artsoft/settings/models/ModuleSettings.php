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

    public $shelf_life_pass;
    public $shelf_life_attendlog;
    public $shelf_life_sitelog;
    public $shelf_life_requestlog;

    public $pre_status;
    public $pre_date_in;
    public $pre_date_out;
    public $pre_plan_year;
    public $pre_date_start;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in'], 'required'],
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in'], 'string'],

                [['shelf_life_pass', 'shelf_life_attendlog', 'shelf_life_sitelog', 'shelf_life_requestlog'], 'required'],
                [['shelf_life_pass', 'shelf_life_attendlog', 'shelf_life_sitelog', 'shelf_life_requestlog'], 'string'],

                [['pre_status', 'pre_date_in', 'pre_date_out', 'pre_plan_year', 'pre_date_start'], 'required'],
                [['pre_status'], 'boolean'],
                [['pre_date_in', 'pre_date_out', 'pre_date_start'], 'date'],
                [['pre_plan_year'], 'string'],
            ]);
    }

    public function attributeLabels()
    {
        return [
            'day_in' => 'День начала периода',
            'day_out' => 'День окончания периода',
            'student_delta_time' => 'Возможный допуск на отклонение от полного времени проведения занятия',
            'study_plan_month_in' => 'Месяц начала учебного года(расчетного периода)',
            'shelf_life_pass' => 'Срок хранения проходов через СКУД (дней)',
            'shelf_life_attendlog' => 'Срок хранения Журнала выдачи ключей (дней)',
            'shelf_life_sitelog' => 'Срок хранения Лога посещения сайта (дней)',
            'shelf_life_requestlog' => 'Срок хранения Лога запросов (дней)',

            'pre_status' => 'Статус набора',
            'pre_date_in' => 'Открытие формы предварительной записи',
            'pre_date_out' => 'Закрытие формы предварительной записи',
            'pre_plan_year' => 'Учебный год',
            'pre_date_start' => 'Начало обучения',
        ];
    }

}

