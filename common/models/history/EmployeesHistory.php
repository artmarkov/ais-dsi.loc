<?php

namespace common\models\history;

use common\models\employees\Employees;
use common\widgets\history\BaseHistory;

class EmployeesHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'employees_hist';
    }

    public static function getModelName()
    {
        return Employees::class;
    }

    protected function getFields()
    {
        return [
            'position',
        ];
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        $id = $this->getModelName()::findOne($this->objId)->user->id;
        $vf = new UserCommonHistory($id);
        $selfHistory = array_merge($selfHistory, $vf->getHistory());

        foreach (UsersCardHistory::getLinkedIdList('user_common_id', $id) as $cardId) {
            $vf = new UsersCardHistory($cardId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}