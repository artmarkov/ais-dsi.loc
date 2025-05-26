<?php

namespace common\models\history;

use common\models\entrant\EntrantMembers;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;

class EntrantMembersHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_members_hist';
    }

    public static function getModelName()
    {
        return EntrantMembers::class;
    }

    protected function getFields()
    {
        return [
//            'entrant_id',
            'members_id',
            'mark_rem',
        ];
    }
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'members_id':
                return UserCommon::findOne(['user_id' => $model->members_id]) ? UserCommon::findOne(['user_id' => $model->members_id])->getLastFM() : $model->members_id;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();
        foreach (EntrantTestHistory::getLinkedIdList('entrant_members_id', $this->objId) as $itemId) {
            $vf = new EntrantTestHistory($itemId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}