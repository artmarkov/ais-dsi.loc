<?php

namespace common\models\history;

use common\models\parents\Parents;
use common\widgets\history\BaseHistory;

class ParentsHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'parents_hist';
    }

    public static function getModelName()
    {
        return Parents::class;
    }

    protected function getFields()
    {
        return [
            'position_id',
            'sert_name',
            'sert_series',
            'sert_num',
            'sert_organ',
            'sert_date',
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
            case 'position_id':
                return isset($model->position_id) ? $model->position->name : $value;
            case 'sert_name':
                return isset(self::getModelName()::PARENTS_DOC[$value]) ? self::getModelName()::PARENTS_DOC[$value] : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
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

//        foreach (TeachersActivityHistory::getLinkedIdList('teachers_id', $this->objId) as $teachersId) {
//            $vf = new TeachersActivityHistory($teachersId);
//            $selfHistory = array_merge($selfHistory, $vf->getHistory());
//        }

        krsort($selfHistory);
        return $selfHistory;
    }
}