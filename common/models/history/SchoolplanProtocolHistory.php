<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\schoolplan\SchoolplanProtocol;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class SchoolplanProtocolHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'schoolplan_protocol_hist';
    }

    public static function getModelName()
    {
        return SchoolplanProtocol::class;
    }

    protected function getFields()
    {
        return [
            'protocol_name',
            'description',
            'protocol_date',
            'leader_id',
            'secretary_id',
            'members_list',
            'subject_list',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'leader_id':
                return isset($model->leader_id) ? (UserCommon::findOne(['user_id' => $model->leader_id]) ? UserCommon::findOne(['user_id' => $model->leader_id])->getFullName() : $model->leader_id) : null;
            case 'secretary_id':
                return isset($model->secretary_id) ? (UserCommon::findOne(['user_id' => $model->secretary_id]) ? UserCommon::findOne(['user_id' => $model->secretary_id])->getFullName() : $model->secretary_id) : null;
            case 'members_list':
                if (isset($model->members_list)) {
                    $v = [];
                    foreach (Json::decode($model->members_list) as $id) {
                        $v[] = $id != null ? (UserCommon::findOne(['user_id' => $id]) ? UserCommon::findOne(['user_id' => $id])->getFullName() : $id) : null;
                    }
                    return implode(', ', $v);
                }
            case 'subject_list':
                if (isset($model->subject_list)) {
                    $v = [];
                    foreach (Json::decode($model->subject_list) as $id) {
                        $v[] = $id != null ? RefBook::find('subject_name')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }

        }
        return parent::getDisplayValue($model, $name, $value);
    }

//    /**
//     * @return array
//     */
//    public function getHistory()
//    {
//        $selfHistory = parent::getHistory();
//
//        foreach (EntrantMembersHistory::getLinkedIdList('entrant_id', $this->objId) as $itemId) {
//            $vf = new EntrantMembersHistory($itemId);
//            $selfHistory = array_merge($selfHistory, $vf->getHistory());
//        }
//
//        krsort($selfHistory);
//        return $selfHistory;
//    }
}