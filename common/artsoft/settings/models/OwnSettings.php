<?php

namespace artsoft\settings\models;

use yii\helpers\ArrayHelper;

class OwnSettings extends BaseSettingsModel
{
    const GROUP = 'own';

    public $name;
    public $shortname;
    public $address;
    public $email;
    public $head;
    public $chief_accountant;
    public $chief_accountant_post;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['name', 'shortname', 'address', 'email', 'head', 'chief_accountant', 'chief_accountant_post'], 'required'],
                [['name', 'shortname', 'address', 'email', 'head', 'chief_accountant', 'chief_accountant_post'], 'string'],
            ]);
    }


    public function attributeLabels()
    {
        return [
            'name' => 'Наименование учреждения',
            'shortname' => 'Сокращенное наименование учреждения',
            'address' => 'Почтовый адрес учреждения',
            'email' => 'E-mail учреждения',
            'head' => 'Руководитель учреждения',
            'chief_accountant' => 'Главный бухгалтер',
            'chief_accountant_post' => 'Бухгалтер',
        ];
    }

}

