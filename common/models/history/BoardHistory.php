<?php

namespace common\models\history;

use common\models\info\Board;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

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
                return isset($model->category_id) ? $model::getCategoryValue($value) : $value;
            case 'importance_id':
                return isset($model->importance_id) ? $model::getImportanceList()[$value] : $value;
            case 'author_id':
                return isset($model->author->userCommon) ? $model->author->userCommon->fullName : $value;
            case 'recipients_list':
                if (isset($model->recipients_list)) {
                    $v = [];
                    foreach (Json::decode($model->recipients_list) as $id) {
                        $v[] = $id != null ? (UserCommon::findOne(['user_id' => $id]) ? UserCommon::findOne(['user_id' => $id])->getFullName() : $id) : null;
                    }
                    return implode(', ', $v);
                }
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}