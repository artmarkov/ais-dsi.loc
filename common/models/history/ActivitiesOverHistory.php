<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\activities\ActivitiesOver;
use common\models\own\Department;
use common\models\user\UserCommon;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class ActivitiesOverHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'activities_over_hist';
    }

    public static function getModelName()
    {
        return ActivitiesOver::class;
    }

    protected function getFields()
    {
        return [
            'title',
            'datetime_in',
            'datetime_out',
            'auditory_id',
            'department_list',
            'executors_list',
            'description',
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
            case 'department_list':
                if (isset($model->department_list)) {
                    $v = [];
                    foreach (Json::decode($model->department_list) as $id) {
                        $v[] = $id != null ? Department::findOne($id)->name : null;
                    }
                    return implode(', ', $v);
                }
                break;
            case 'executors_list':
                if (isset($model->executors_list)) {
                    $v = [];
                    foreach (Json::decode($model->executors_list) as $id) {
                        $v[] = $id != null ? UserCommon::findOne($id)->getFullName() : null;
                    }
                    return implode(', ', $v);
                }
                break;
            case 'auditory_id':
                return isset($model->auditory_id) ? RefBook::find('auditory_memo_1')->getValue($model->auditory_id) : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }
}