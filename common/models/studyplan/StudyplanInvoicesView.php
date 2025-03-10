<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "studyplan_invoices_view".
 *
 * @property int|null $studyplan_id
 * @property int|null $programm_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $course
 * @property int|null $status
 * @property int|null $subject_form_id
 * @property int|null $studyplan_mat_capital_flag
 * @property int|null $education_cat_id
 * @property int|null $programm_short_name
 * @property int|null $education_cat_short_name
 * @property int|null $student_fio
 * @property string|null $studyplan_subjects
 * @property string|null $subject_list
 * @property string|null $subject_type_list
 * @property string|null $limited_status_list
 * @property string|null $direction_list
 * @property string|null $teachers_list
 * @property int|null $studyplan_invoices_id
 * @property int|null $invoices_id
 * @property int|null $studyplan_invoices_status
 * @property int|null $month_time_fact
 * @property float|null $invoices_summ
 * @property int|null $invoices_date
 * @property int|null $payment_time
 * @property int|null $payment_time_fact
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
            $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');
            $attr['programm_id'] = 'Обр. программа';
            $attr['education_cat_id'] = Yii::t('art/guide', 'Education Cat');
            $attr['student_id'] = Yii::t('art/student', 'Student');
            $attr['student_fio'] = Yii::t('art/student', 'Student');
            $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
            $attr['course'] = Yii::t('art/guide', 'Course');
            $attr['status'] = Yii::t('art/guide', 'Status');
            $attr['subject_form_id'] = Yii::t('art/guide', 'Subject Form');
            $attr['education_cat_id'] = 'Вид прогр.';
            $attr['studyplan_subjects'] = 'Дисциплины';
            $attr['subject_list'] = 'Предметы';
            $attr['subject_type_list'] = 'Тип занятия';
            $attr['limited_status_list'] = 'Ограничения';
            $attr['direction_list'] = Yii::t('art/teachers', 'Direction');
            $attr['teachers_list'] = Yii::t('art/teachers', 'Teacher');
            $attr['studyplan_invoices_id'] ='ID';
            $attr['studyplan_invoices_status'] = 'Статус';
            $attr['studyplan_mat_capital_flag'] =  Yii::t('art/studyplan', 'Mat Capital');
        return $attr;
    }

    public static function getTotalSumm($provider)
    {
        $total = [0,0];
        foreach ($provider as $item) {
            if (in_array($item['studyplan_invoices_status'], [self::STATUS_WORK, self::STATUS_ARREARS])) {
                $total[0] += $item['invoices_summ'];
            } elseif (in_array($item['studyplan_invoices_status'], [self::STATUS_RECEIPT, self::STATUS_PAYD])) {
                $total[1] += $item['invoices_summ'];
            }
        }

        return $total[1] . '/' . $total[0];
    }
}
