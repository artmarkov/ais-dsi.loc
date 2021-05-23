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
            case 'sert_name':
                return isset(self::getModelName()::PARENT_DOC[$value]) ? self::getModelName()::PARENT_DOC[$value] : $value;
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

      foreach (ParentDependenceHistory::getLinkedIdList('parent_id', $this->objId) as $parentId) {
            $vf = new ParentDependenceHistory($parentId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}