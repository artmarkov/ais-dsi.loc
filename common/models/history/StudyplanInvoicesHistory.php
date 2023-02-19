<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\studyplan\StudyplanInvoices;
use common\widgets\history\BaseHistory;

class StudyplanInvoicesHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'studyplan_invoices_hist';
    }

    public static function getModelName()
    {
        return StudyplanInvoices::class;
    }

    protected function getFields()
    {
        return [
            'studyplan_id',
            'invoices_id',
            'direction_id',
            'teachers_id',
            'type_id',
            'vid_id',
            'month_time_fact',
            'invoices_tabel_flag',
            'invoices_date',
            'invoices_summ',
            'payment_time',
            'payment_time_fact',
            'invoices_app',
            'invoices_rem',
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
        switch ($name) {
            case 'studyplan_id':
                return isset($model->studyplan_id) ? $model->studyplan->student->fullName : $value;
            case 'invoices_id':
                return isset($model->invoices_id) ? $model->invoices->name : $value;
            case 'direction_id':
                return isset($model->direction_id) ? $model->direction->name : $value;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
            case 'type_id':
                return isset($model->type_id) ? $model->subjectType->name : $value;
            case 'vid_id':
                return isset($model->vid_id) ? $model->subjectVid->name : $value;
            case 'status':
                return isset($model->status) ? StudyplanInvoices::getStatusValue($value) : $value;
            case 'invoices_tabel_flag':
                return isset($model->invoices_tabel_flag) ? ($model->invoices_tabel_flag ? 'Да' : 'Нет') : $value;
        case 'invoices_date':
        case 'payment_time':
        case 'payment_time_fact':
                return isset($value) ? \Yii::$app->formatter->asDate($value, 'php:d.m.Y H:i') : null;
        }
        return parent::getDisplayValue($model, $name, $value);
    }


}