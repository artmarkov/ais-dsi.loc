<?php

namespace common\models\history;

use common\models\students\StudentDependence;
use common\widgets\history\BaseHistory;

class StudentDependenceHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'student_dependence_hist';
    }

    public static function getModelName()
    {
        return StudentDependence::class;
    }

    protected function getFields()
    {
        return [
            'parent_id',
            'relation_id',
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
            case 'parent_id':
                return isset($model->parent_id) ? $model->parent->fullName : $value;
            case 'relation_id':
                return isset($model->relation_id) ? $model->relation0->name : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}