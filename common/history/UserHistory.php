<?php
/**
 * Created by IntelliJ IDEA.
 * User: Aleksey
 * Date: 16.08.2018
 * Time: 14:24
 */

namespace common\history;


use artsoft\models\User;

class UserHistory extends BaseHistory
{

    public static function getTableName()
    {
        return 'users_hist';
    }

    public static function getModelName()
    {
        return User::className();
    }

    protected function getFields()
    {
        return [
            'id',
            'username',
            'first_name',
            'middle_name',
            'last_name',
        ];
    }

    /**
     * @param Activity $model
     * @param string $name
     * @param string $value
     * @return string
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'first_name':
                return $model->first_name ? $model->first_name : $value;
            case 'middle_name':
                return $model->middle_name ? $model->middle_name : '';
            case 'last_name':
                return $model->last_name ? $model->last_name : '';
        }

        return parent::getDisplayValue($model, $name, $value);
    }


}