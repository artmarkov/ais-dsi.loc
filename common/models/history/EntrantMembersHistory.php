<?php

namespace common\models\history;

use common\models\entrant\EntrantMembers;
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
//            'members_id',
            'mark_rem',
        ];
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