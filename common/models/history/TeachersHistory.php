<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use common\models\guidejob\Bonus;
use common\models\own\Department;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\db\Query;
use yii\helpers\ArrayHelper;
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
            'work_id',
            'tab_num',
            'department_list',
            'year_serv',
            'year_serv_spec',
            'date_serv',
            'date_serv_spec',
            'bonus_list',
            'bonus_summ',
            'bonus_summ_abs',
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
                        $v[] = $id != null ? Bonus::findOne($id)->name : null;
                    }
                    return implode(', ', $v);
                }
            case 'department_list':
                if (isset($model->department_list)) {
                    $v = [];
                    foreach (Json::decode($model->department_list) as $id) {
                        $v[] = $id != null ? Department::findOne($id)->name : null;
                    }
                    return implode(', ', $v);
                }
            case 'level_id':
                return isset($model->level_id) ? $model->level->name : $value;
            case 'position_id':
                return isset($model->position_id) ? $model->position->name : $value;
            case 'work_id':
                return isset($model->work_id) ? $model->work->name : $value;
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

        foreach (UsersCardHistory::getLinkedIdList('user_common_id', $id) as $cardId) {
            $vf = new UsersCardHistory($cardId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        foreach (TeachersActivityHistory::getLinkedIdList('teachers_id', $this->objId) as $teachersId) {
            $vf = new TeachersActivityHistory($teachersId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }

    /** Получаем список преподавателей,уволенных в этом году
     * @param bool $plan_year
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getTeachersListHist($plan_year = null)
    {
        $timestamp = ArtHelper::getStudyYearParams($plan_year, $month_dev = null);
        $date_in = $timestamp['timestamp_in'];
        $date_out = $timestamp['timestamp_out'];

        $ids = \Yii::$app->getDb()->createCommand('
            select DISTINCT h.id
            from teachers_hist h 
            inner join user_common_hist u ON u.id = h.user_common_id
            where u.updated_at between :timestamp_in AND :timestamp_out 
            and u.status = 0
            and u.version = (select MAX(version) from user_common_hist where id = u.id)
            and u.op != \'D\'
            ',
            [
                'timestamp_in' => $date_in,
                'timestamp_out' => $date_out,
            ])
            ->queryColumn();
        return $ids;
    }

    public static function getTeachersQuery($plan_year = null) {
        return (new Query())->from('teachers_view')
            ->select('teachers_id as id, fullname as name')
            ->where(['teachers_id' => self::getTeachersListHist($plan_year)])
            ->orWhere(['status' => UserCommon::STATUS_ACTIVE])
            ->orderBy('fullname')->all();
    }

    public static function getTeachersList($plan_year = null) {
        return ArrayHelper::map(self::getTeachersQuery($plan_year), 'id', 'name');
    }
}