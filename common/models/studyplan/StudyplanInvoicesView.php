<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "studyplan_invoices_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_type_id
 * @property float|null $week_time
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $status
 * @property int|null $teachers_load_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $load_time
 * @property int|null $studyplan_invoices_id
 * @property int|null $invoices_id
 * @property int|null $studyplan_invoices_status
 * @property int|null $month_time_fact
 * @property float|null $invoices_summ
 */
class StudyplanInvoicesView extends StudyplanInvoices
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_invoices_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
            $attr['studyplan_subject_id'] = Yii::t('art/guide', 'Subject Name');
            $attr['subject_type_id'] = Yii::t('art/guide', 'Type Name');
            $attr['week_time'] = Yii::t('art/guide', 'Week Time');
            $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');
            $attr['student_id'] = Yii::t('art/student', 'Student');
            $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
            $attr['status'] = Yii::t('art/guide', 'Status');
            $attr['teachers_load_id'] = Yii::t('art/guide', 'Teachers Load');
            $attr['direction_id'] = Yii::t('art/teachers', 'Name Direction');
            $attr['teachers_id'] = Yii::t('art/teachers', 'Teachers');
            $attr['load_time'] = Yii::t('art/guide', 'Load Time');
            $attr['studyplan_invoices_id'] ='ID';
            $attr['studyplan_invoices_status'] = 'Статус платежа';
        return $attr;
    }
}
