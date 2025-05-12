<?php

namespace common\models\history;

use artsoft\models\User;
use common\models\planfix\Planfix;
use common\models\teachers\Teachers;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class PlanfixHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'planfix_hist';
    }

    public static function getModelName()
    {
        return Planfix::class;
    }

    protected function getFields()
    {
        return [
            'category_id',
            'name',
            'description',
            'planfix_author',
            'executors_list',
            'importance',
            'planfix_date',
            'status_reason',
            'status',
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
        $users_list = User::getUsersListByCategory(['teachers', 'employees'], false);

        switch ($name) {
            case 'executors_list':
                if (isset($model->executors_list)) {
                    $v = [];
                    foreach (Json::decode($model->executors_list) as $id) {
                        $v[] = $users_list[$id] ?? $id;
                    }
                    return implode(', ', $v);
                }
            case 'planfix_author':
                return $users_list[$model->$name] ?? $model->$name;
            case 'category_id':
                return isset($model->category_id) ? $model->category->name : $value;
            case 'status':
                return isset($model->status) ? Planfix::getStatusValue($value) : $value;
            case 'importance':
                return isset($model->status) ? Planfix::getImportanceValue($value) : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
//    public function getHistory()
//    {
//        $selfHistory = parent::getHistory();
//
//        $id = $this->getModelName()::findOne($this->objId)->user->id;
//        $vf = new UserCommonHistory($id);
//        $selfHistory = array_merge($selfHistory, $vf->getHistory());
//
//        foreach (UsersCardHistory::getLinkedIdList('user_common_id', $id) as $cardId) {
//            $vf = new UsersCardHistory($cardId);
//            $selfHistory = array_merge($selfHistory, $vf->getHistory());
//        }
//
//        foreach (TeachersActivityHistory::getLinkedIdList('teachers_id', $this->objId) as $teachersId) {
//            $vf = new TeachersActivityHistory($teachersId);
//            $selfHistory = array_merge($selfHistory, $vf->getHistory());
//        }
//
//        krsort($selfHistory);
//        return $selfHistory;
//    }
}