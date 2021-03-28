<?php

namespace common\widgets\history;

class Item extends \yii\base\Model
{
    public $group;
    public $class;
    public $type;
    public $updated_at;
    public $updated_by;
    public $attr_name;
    public $attr_label;
    public $value_old;
    public $value_new;
    public $display_value_old;
    public $display_value_new;

    public function attributeLabels()
    {
        return [
            'updatedAt' => 'Изменен',
            'updatedBy' => 'Изменил',
            'attrName' => 'код атрибута',
            'attrLabel' => 'Атрибут',
            'valueOld' => 'raw значение было',
            'valueNew' => 'raw значение стало',
            'displayValueOld' => 'Значение было',
            'displayValueNew' => 'Значение стало',
        ];
    }


    public function fields()
    {
        return [
            'updated_at' => function () {
                return \Yii::$app->formatter->asDatetime($this->updated_at, 'php:d-m-Y h:i:s');
            },
            'updated_by_username' => function () {
                return $this->updated_by > 0 ? \artsoft\models\User::findOne($this->updated_by)->username : null;
            },
            'updated_by',
            'attr_name',
            'attr_label',
            'value_old',
            'value_new',
            'display_value_old',
            'display_value_new',
            'class',
            'type',
            'group'
        ];
    }

}