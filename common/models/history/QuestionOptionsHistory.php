<?php

namespace common\models\history;

use common\models\question\QuestionOptions;
use common\widgets\history\BaseHistory;

class QuestionOptionsHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'question_options_hist';
    }

    public static function getModelName()
    {
        return QuestionOptions::class;
    }

    protected function getFields()
    {
        return [
            'name',
            'free_flag',
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
            case 'free_flag':
                return isset($model->free_flag) ? ($model->free_flag ? 'Да' : 'Нет') : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

}