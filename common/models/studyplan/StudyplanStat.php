<?php

namespace common\models\studyplan;

use artsoft\helpers\ExcelObjectList;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\user\UserCommon;
use Yii;
use yii\db\Query;

class StudyplanStat
{
    protected $plan_year;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
    }

    protected function getStudyplans()
    {
        $models = (new Query())->from('studyplan_stat_view')
            ->where(['plan_year' => $this->plan_year])
            ->all();
        return $models;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getData()
    {
//        echo '<pre>' . print_r($this->getStudyplans(), true) . '</pre>';
        $attributes = [
            'created_at' => 'Дата создания плана',
            'student_id' => 'ФЛС',
            'student_fio' => 'ФИО ученика',
            'plan_year' => 'Учебный год',
            'education_programm_name' => 'Образовательная програма',
            'education_programm_short_name' => 'Образовательная програма',
//            'education_cat_name' => 'Категория обр. программы',
            'education_cat_short_name' => 'Категория обр. программы',
            'speciality' => 'Специальность',
            'speciality_teachers_fio' => 'Преподаватель по специальности',
            'course' => 'Класс',
            'description' => 'Описание',
            'year_time_total' => 'Всего учебных часов в год',
            'cost_month_total' => 'Сумма в рублях за месяц',
            'cost_year_total' => 'Сумма в рублях за учебный год',
            'doc_date' => 'Дата документов',
            'doc_contract_start' => 'Дата начала действия договора',
            'doc_contract_end' => 'Дата окончания действия договора',
            'status' => 'Статус учебного плана',
            'status_reason' => 'Причина закрытия учебного плана',
            'subject_form_name' => 'Форма обучения',
            'student_address' => 'Адресс ученика',
            'student_birth_date' => 'Дата рождения ученика',
            'student_birth_age' => 'Возраст ученика',
            'student_gender' => 'Пол ученика',
            'student_phone' => 'Телефон ученика',
            'student_phone_optional' => 'Телефон ученика доп.',
            'student_snils' => 'СНИЛС ученика',
            'student_info' => 'Информация ученика',
            'student_email' => 'ЕМАЙЛ ученика',
            'student_sert_name' => 'Название документа ученика',
            'student_sert_doc' => 'Данные документа ученика',
//            'student_sert_series' => '',
//            'student_sert_num' => '',
//            'student_sert_organ' => '',
//            'student_sert_date' => '',
            'limited_status_list' => 'Дополнительные сведения',
            'signer_fio' => 'Подписант документов',
            'signer_address' => 'Адресс родителя',
            'signer_birth_date' => 'Дата рождения родителя',
            'signer_gender' => 'Пол родителя',
            'signer_phone' => 'Телефон родителя',
            'signer_phone_optional' => 'Телефон родителя доп.',
            'signer_snils' => 'СНИЛС родителя',
            'signer_info' => 'Информация родителя',
            'signer_email' => 'ЕМАЙЛ родителя',
            'signer_sert_name' => 'Название документа родителя',
            'signer_doc' => 'Данные документа родителя',
//            'signer_sert_series=> '',
//            'signer_sert_num=> '',
//            'signer_sert_organ=> '',
//            'signer_sert_date=> '',
//            'signer_sert_code=> ''
        ];
        $data = [];
        foreach ($this->getStudyplans() as $id => $model) {
            $age = \artsoft\helpers\ArtHelper::age($model['student_birth_date']);
            $limit = [];
            foreach (explode(',', $model['limited_status_list']) as $item => $status) {
                $limit[] = Student::getLimitedStatusValue($status);
            }
            $data[$id]['created_at'] = Yii::$app->formatter->asDate($model['created_at']);
            $data[$id]['student_id'] = sprintf('%06d', $model['student_id']);
            $data[$id]['student_fio'] = $model['student_fio'];
            $data[$id]['education_programm_name'] = $model['education_programm_name'];
            $data[$id]['education_programm_short_name'] = $model['education_programm_short_name'];
            $data[$id]['education_cat_name'] = $model['education_cat_name'];
            $data[$id]['education_cat_short_name'] = $model['education_cat_short_name'];
            $data[$id]['speciality'] = $model['speciality'];
            $data[$id]['speciality_teachers_fio'] = $model['speciality_teachers_fio'];
            $data[$id]['course'] = $model['course'];
            $data[$id]['subject_form_name'] = $model['subject_form_name'];
            $data[$id]['plan_year'] = $model['plan_year'];
            $data[$id]['description'] = $model['description'];
            $data[$id]['year_time_total'] = $model['year_time_total'];
            $data[$id]['cost_month_total'] = $model['cost_month_total'];
            $data[$id]['cost_year_total'] = $model['cost_year_total'];
            $data[$id]['doc_date'] = Yii::$app->formatter->asDate($model['doc_date']);
            $data[$id]['doc_contract_start'] = $model['doc_contract_start'];
            $data[$id]['doc_contract_end'] = $model['doc_contract_end'];
            $data[$id]['status'] = Studyplan::getStatusValue($model['status']);
            $data[$id]['status_reason'] = Studyplan::getStatusReasonValue($model['status_reason']);
            $data[$id]['student_address'] = $model['student_address'];
            $data[$id]['student_birth_date'] = Yii::$app->formatter->asDate($model['student_birth_date']);
            $data[$id]['student_birth_age'] = $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.';
            $data[$id]['student_gender'] = UserCommon::getGenderValue($model['student_gender']);
            $data[$id]['student_phone'] = $model['student_phone'];
            $data[$id]['student_phone_optional'] = $model['student_phone_optional'];
            $data[$id]['student_snils'] = $model['student_snils'];
            $data[$id]['student_info'] = $model['student_info'];
            $data[$id]['student_email'] = $model['student_email'];
            $data[$id]['student_sert_name'] = Student::getDocumentValue($model['student_sert_name']);
            $data[$id]['student_sert_doc'] = $model['student_sert_series'] ? 'серия ' . $model['student_sert_series'] . ' №' . $model['student_sert_num'] . ' выдан ' . $model['student_sert_organ'] . ' ' . Yii::$app->formatter->asDate($model['student_sert_date']) : '';
            $data[$id]['limited_status_list'] = implode(',', $limit);
            $data[$id]['signer_fio'] = $model['signer_fio'];
            $data[$id]['signer_address'] = $model['signer_address'];
            $data[$id]['signer_birth_date'] = Yii::$app->formatter->asDate($model['signer_birth_date']);
            $data[$id]['signer_gender'] = UserCommon::getGenderValue($model['signer_gender']);
            $data[$id]['signer_phone'] = $model['signer_phone'];
            $data[$id]['signer_phone_optional'] = $model['signer_phone_optional'];
            $data[$id]['signer_snils'] = $model['signer_snils'];
            $data[$id]['signer_info'] = $model['signer_info'];
            $data[$id]['signer_email'] = $model['signer_email'];
            $data[$id]['signer_sert_name'] = Parents::getDocumentValue($model['signer_sert_name']);
            $data[$id]['signer_doc'] = $model['signer_sert_series'] ? 'серия ' . $model['signer_sert_series'] . ' №' . $model['signer_sert_num'] . ' выдан ' . $model['signer_sert_organ'] . ' ' . Yii::$app->formatter->asDate($model['signer_sert_date']) . ' код ' . $model['signer_sert_code'] : '';
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });

        return ['data' => $data, 'attributes' => $attributes];
    }

    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }
//            $x->addData(['stake' => 'Итого', 'total' => $data['all_summ']]);

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_studyplan-stat.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }

}
