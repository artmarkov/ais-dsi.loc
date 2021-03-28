<?php

namespace common\models\history;

use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;

class UserCommonHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'user_common_hist';
    }

    public static function getModelName()
    {
        return UserCommon::class;
    }

    protected function getFields()
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'birth_date',
            'status',
            'snils',
            'phone',
            'phone_optional',
            'snils',
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
            case 'user_category':
                return isset($model->user_category) ? $model->getUserCategoryValue($model->user_category) : $value;
            case 'status':
                return isset($model->status) ? $model->getStatusValue($model->status) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}