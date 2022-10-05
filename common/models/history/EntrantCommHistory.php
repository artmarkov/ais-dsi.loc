<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\entrant\EntrantComm;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class EntrantCommHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_comm_hist';
    }

    public static function getModelName()
    {
        return EntrantComm::class;
    }

    protected function getFields()
    {
        return [
            'division_id',
            'plan_year',
            'name',
            'leader_id',
            'secretary_id',
            'members_list',
            'prep_on_test_list',
            'prep_off_test_list',
            'timestamp_in',
            'timestamp_out',
            'description',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'leader_id':
                return isset($model->leader_id) ? (UserCommon::findOne(['user_id' => $model->leader_id]) ? UserCommon::findOne(['user_id' => $model->leader_id])->getFullName() : $model->leader_id) : null;
            case 'secretary_id':
                return isset($model->secretary_id) ? (UserCommon::findOne(['user_id' => $model->secretary_id]) ? UserCommon::findOne(['user_id' => $model->secretary_id])->getFullName() : $model->secretary_id) : null;
            case 'division_id':
                return isset($model->division_id) ? RefBook::find('division_name')->getValue($model->division_id) : $value;
            case 'plan_year':
                return isset($model->plan_year) ? ArtHelper::getStudyYearsList()[$value] : $value;
            case 'members_list':
                if (isset($model->members_list)) {
                    $v = [];
                    foreach (Json::decode($model->members_list) as $id) {
                        $v[] = $id != null ? (UserCommon::findOne(['user_id' => $id]) ? UserCommon::findOne(['user_id' => $id])->getFullName() : $id) : null;
                    }
                    return implode(', ', $v);
                }
            case 'prep_on_test_list':
                if (isset($model->prep_on_test_list)) {
                    $v = [];
                    foreach (Json::decode($model->prep_on_test_list) as $id) {
                        $v[] = $id != null ? RefBook::find('entrant_test_name')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }
            case 'prep_off_test_list':
                if (isset($model->prep_off_test_list)) {
                    $v = [];
                    foreach (Json::decode($model->prep_off_test_list) as $id) {
                        $v[] = $id != null ? RefBook::find('entrant_test_name')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}