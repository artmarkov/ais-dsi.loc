<?php

namespace artsoft\settings\models;

use yii\helpers\ArrayHelper;

class MailingSettings extends BaseSettingsModel
{
    const GROUP = 'mailing';

    public $mailing_birthday;
    public $mailing_birthday_period;

    public $schoolplan_perform_doc;
    public $confirm_progress_perform_doc;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
//                [['mailing_birthday'], 'required'],
                [['mailing_birthday', 'mailing_birthday_period'], 'string'],
                [['schoolplan_perform_doc','confirm_progress_perform_doc'], 'boolean'],
            ]);
    }

    public function attributeLabels()
    {
        return [
            'mailing_birthday' => 'Дни рождения сегодня',
            'mailing_birthday_period' => 'Дни рождения за месяц',

            'schoolplan_perform_doc' => 'Включить уведомления в модуле "Выполнение плана и участие в мероприятии"',
            'confirm_progress_perform_doc' => 'Включить уведомления в модуле "Проверка журнала успеваемости"',
        ];
    }

}

