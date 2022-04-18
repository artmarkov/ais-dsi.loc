<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\studyplan\SubjectCharacteristic;
use common\widgets\history\BaseHistory;

class SubjectCharacteristicHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'subject_characteristic_hist';
    }

    public static function getModelName()
    {
        return SubjectCharacteristic::class;
    }

    protected function getFields()
    {
        return [
            'studyplan_subject_id',
            'teachers_id',
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
            case 'studyplan_subject_id':
                return isset($model->studyplan_subject_id) ? RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id) : $value;
                break;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
                break;

        }
        return parent::getDisplayValue($model, $name, $value);
    }
}