<?php

namespace common\models\history;

use common\models\guidejob\Bonus;
use common\models\own\Department;
use common\models\teachers\Teachers;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class TeachersHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_hist';
    }

    public static function getModelName()
    {
        return Teachers::class;
    }

    protected function getFields()
    {
        return [
            'position_id',
            'level_id',
            'tab_num',
            'department_list',
            'year_serv',
            'year_serv_spec',
            'date_serv',
            'date_serv_spec',
            'bonus_list',
            'bonus_summ',
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
            case 'bonus_list':
                if (isset($model->bonus_list)) {
                    $v = [];
                    foreach (Json::decode($model->bonus_list) as $id) {
                        $v[] = Bonus::findOne($id)->name;
                    }
                    return implode(', ', $v);
                }
            case 'department_list':
                if (isset($model->department_list)) {
                    $v = [];
                    foreach (Json::decode($model->department_list) as $id) {
                        $v[] = Department::findOne(['id' => $id])->name;
                    }
                    return implode(', ', $v);
                }
            case 'level_id':
                return isset($model->level_id) ? $model->level->name : $value;
            case 'position_id':
                return isset($model->position_id) ? $model->position->name : $value;
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

        foreach (TeachersActivityHistory::getLinkedIdList('teachers_id', $this->objId) as $teachersId) {
            $vf = new TeachersActivityHistory($teachersId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}