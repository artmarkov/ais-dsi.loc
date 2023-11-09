<?php

namespace artsoft\settings\models;

use yii\helpers\ArrayHelper;

class MailingSettings extends BaseSettingsModel
{
    const GROUP = 'mailing';

    public $mailing_birthday;
    public $mailing_birthday_period;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
//                [['mailing_birthday'], 'required'],
                [['mailing_birthday', 'mailing_birthday_period'], 'string'],
            ]);
    }

    public function attributeLabels()
    {
        return [
            'mailing_birthday' => 'Дни рождения сегодня',
            'mailing_birthday_period' => 'Дни рождения за месяц',
        ];
    }

}

