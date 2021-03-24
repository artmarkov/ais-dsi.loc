<?php

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
        return User::class;
    }

    protected function getFields()
    {
        return [
            'id',
            'username',
            'first_name',
            'middle_name',
            'last_name',
            'birth_timestamp',
            'email',
            'status',
            'snils',
            'phone',
            'phone_optional',
            'skype',
            'info',
            'user_category',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'birth_timestamp':
                return $model->birth_timestamp ? \Yii::$app->formatter->asDate($model->birth_timestamp) : $value;
            case 'user_category':
                return isset($model->user_category) ? $model->getUserCategoryValue($model->user_category) : $value;
            case 'status':
                return isset($model->status) ? $model->getStatusValue($model->status) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}