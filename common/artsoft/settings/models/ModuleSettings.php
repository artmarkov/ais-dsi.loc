<?php

namespace artsoft\settings\models;

use yii\helpers\ArrayHelper;

class ModuleSettings extends BaseSettingsModel
{
    const GROUP = 'module';

    public $day_in;
    public $day_out;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['day_in', 'day_out'], 'required'],
                [['day_in', 'day_out'], 'string'],
            ]);
    }


    public function attributeLabels()
    {
        return [
            'day_in' => 'День начала периода',
            'day_out' => 'День окончания периода',
        ];
    }

}

