<?php

namespace  backend\widgets\fullcalendarscheduler\src\models;

/**
 * Class CalendarModel
 * @package  backend\widgets\fullcalendarscheduler\src\models
 */
class CalendarModel extends \yii\base\Model
{
    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        foreach ($fields as $field) {
            // If the attribute is not set don't return it in the response
            if (is_null($this->$field)) {
                unset($fields[$field]);
            }
        }
        return $fields;
    }
}