<?php

namespace common\models\history;

use common\models\entrant\EntrantGroup;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class EntrantGroupHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_group_hist';
    }

    public static function getModelName()
    {
        return EntrantGroup::class;
    }

    protected function getFields()
    {
        return [
            'name',
            'prep_flag',
            'timestamp_in',
            'description',
            'group_secretary_id',
            'group_members_list',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'group_secretary_id':
                return isset($model->group_secretary_id) ? (UserCommon::findOne(['user_id' => $model->group_secretary_id]) ? UserCommon::findOne(['user_id' => $model->group_secretary_id])->getFullName() : $model->group_secretary_id) : null;
            case 'group_members_list':
                if (isset($model->group_members_list)) {
                    $v = [];
                    foreach (Json::decode($model->group_members_list) as $id) {
                        $v[] = $id != null ? (UserCommon::findOne(['user_id' => $id]) ? UserCommon::findOne(['user_id' => $id])->getFullName() : $id) : null;
                    }
                    return implode(', ', $v);
                }

            case 'prep_flag':
                return isset($model->prep_flag) ? EntrantGroup::getPrepValue($model->prep_flag) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}