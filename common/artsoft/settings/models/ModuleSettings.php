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
    public $study_plan_month_in_frontend;
    public $thematic_template_author_flag;
    public $attestation_on;

    public $shelf_life_pass;
    public $shelf_life_attendlog;
    public $shelf_life_sitelog;
    public $shelf_life_requestlog;
    public $shelf_life_dbdump;

    public $pre_status;
    public $pre_date_in;
    public $pre_date_out;
    public $pre_plan_year;
    public $pre_date_start;

    public $debtors_days;

    public $generator_time_limit;
    public $generator_time_per;
    public $generator_time_max;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in', 'study_plan_month_in_frontend'], 'required'],
                [['day_in', 'day_out', 'student_delta_time', 'study_plan_month_in', 'study_plan_month_in_frontend'], 'string'],

                [['shelf_life_pass', 'shelf_life_attendlog', 'shelf_life_sitelog', 'shelf_life_requestlog', 'shelf_life_dbdump'], 'required'],
                [['shelf_life_pass', 'shelf_life_attendlog', 'shelf_life_sitelog', 'shelf_life_requestlog', 'shelf_life_dbdump'], 'string'],

                [['pre_status', 'pre_date_in', 'pre_date_out', 'pre_plan_year', 'pre_date_start'], 'required'],
                [['pre_status', 'pre_plan_year'], 'string'],
                [['pre_date_in', 'pre_date_out', 'pre_date_start'], 'date'],

                [['debtors_days'], 'string'],
                [['generator_time_limit','generator_time_per','generator_time_max'], 'string'],
            ]);
    }

    public function attributeLabels()
    {
        return [
            'day_in' => 'День начала периода',
            'day_out' => 'День окончания периода',
            'student_delta_time' => 'Возможный допуск на отклонение от полного времени проведения занятия',

            'study_plan_month_in' => 'Месяц начала учебного года(расчетного периода)',
            'study_plan_month_in_frontend' => 'Месяц начала учебного года(фронтенд)',
            'thematic_template_author_flag' => 'Шаблоны тематических планов',
            'attestation_on' => 'Доступ к выставлению оценок ПА преподавателям',


            'shelf_life_pass' => 'Срок хранения проходов через СКУД (дней)',
            'shelf_life_attendlog' => 'Срок хранения Журнала выдачи ключей (дней)',
            'shelf_life_sitelog' => 'Срок хранения Лога посещения сайта (дней)',
            'shelf_life_requestlog' => 'Срок хранения Лога запросов (дней)',
            'shelf_life_dbdump' => 'Срок хранения Дампов БД (дней)',

            'pre_status' => 'Статус набора',
            'pre_date_in' => 'Открытие формы предварительной записи',
            'pre_date_out' => 'Закрытие формы предварительной записи',
            'pre_plan_year' => 'Учебный год',
            'pre_date_start' => 'Начало обучения',

            'debtors_days' => 'Установить задолженность по оплате по истечении (дней)',

            'generator_time_limit' => 'Время работы до перерыва (часов)',
            'generator_time_per' => 'Время перерыва (мин)',
            'generator_time_max' => 'Окончание работы (час)',
        ];
    }

}

