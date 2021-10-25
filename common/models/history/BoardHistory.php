<?php

namespace common\models\history;

use common\models\info\Board;
use common\widgets\history\BaseHistory;

class BoardHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'board_hist';
    }

    public static function getModelName()
    {
        return Board::class;
    }

    protected function getFields()
    {
        return [
            'author_id',
            'category_id',
            'importance_id',
            'title',
            'description',
            'recipients_list',
            'board_date',
            'delete_date',
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
            case 'category_id':
                return isset($model->category_id) ? $model->getCategoryValue() : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }
}