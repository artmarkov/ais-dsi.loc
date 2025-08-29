<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\teachers\TeachersQualifications;
use common\widgets\history\BaseHistory;

class QualificationsHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'teachers_qualifications_hist';
    }

    public static function getModelName()
    {
        return TeachersQualifications::class;
    }

    protected function getFields()
    {
        return [
            'teachers_id',
            'date',
            'name',
            'place',
            'description',
            'status',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return null
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
            case 'status':
                return isset($model->status) ? TeachersQualifications::getStatusValue($value) : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }
}