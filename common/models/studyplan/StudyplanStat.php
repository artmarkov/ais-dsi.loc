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
    protected $options;

    const OPTIONS_FIELDS = [
        'student_created_at' => 'Дата создания учетной записи',
        'studyplan_created_at' => 'Дата создания плана',
        'student_id' => 'ФЛС',
        'student_fio' => 'ФИО ученика',
        'student_last_name' => 'Фамилия ученика',
        'student_first_name' => 'Имя ученика',
        'student_middle_name' => 'Отчество ученика',
        'plan_year' => 'Учебный год',
        'education_programm_name' => 'Образовательная программа',
        'education_programm_short_name' => 'Образовательная программа сокр.',
        'education_cat_name' => 'Категория обр. программы',
        'education_cat_short_name' => 'Категория обр. программы сокр.',
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
        'student_address' => 'Адрес ученика',
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
        'signer_fio' => 'Родитель (Подписант документов)',
        'signer_address' => 'Адрес родителя',
        'signer_birth_date' => 'Дата рождения родителя',
        'signer_gender' => 'Пол родителя',
        'signer_phone' => 'Телефон родителя',
        'signer_phone_optional' => 'Телефон родителя доп.',
        'signer_snils' => 'СНИЛС родителя',
        'signer_info' => 'Информация родителя',
        'signer_email' => 'ЕМАЙЛ родителя',
        'signer_sert_name' => 'Название документа родителя',
        'signer_doc' => 'Данные документа родителя',
//            'signer_sert_series'=> '',
//            'signer_sert_num'=> '',
//            'signer_sert_organ'=> '',
//            'signer_sert_date'=> '',
//            'signer_sert_code'=> ''
            'student_social_card_flag' => 'Социальная карта ученика(наличие)',
            'signer_social_card_flag' => 'Социальная карта родителя(наличие)',
    ];

    const OPTIONS_FIELDS_DEFAULT = ['student_created_at', 'studyplan_created_at', 'student_id', 'student_fio', 'plan_year', 'education_programm_short_name', 'education_cat_short_name', 'speciality', 'course', 'description', 'doc_contract_start', 'status', 'subject_form_name', 'student_birth_age', 'student_gender', 'limited_status_list'];

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->options = $model_date->options;
        if (empty($this->options)) {
            throw new \Error(
                'Выберите хотя бы одну опцию!'
            );
        }
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
        $attributes = array_filter(
            self::OPTIONS_FIELDS,
            function ($k) {
                return in_array($k, $this->options);
            },
            ARRAY_FILTER_USE_KEY
        );

        $data = [];
        foreach ($this->getStudyplans() as $id => $model) {
            foreach ($this->options as $item => $option) {
                $data[$id][$option] = $this->getOptionValue($model, $option);
            }
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return ['data' => $data, 'attributes' => $attributes];
    }

    /**
     * @param $model
     * @param $id
     * @param $option
     * @return mixed|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function getOptionValue($model, $option)
    {
        switch ($option) {
            case 'studyplan_created_at' :
            case 'student_created_at' :
            case 'doc_date' :
            case 'student_birth_date' :
            case 'signer_birth_date' :
            case 'doc_contract_start' :
            case 'doc_contract_end' :
                return $model[$option] ? Yii::$app->formatter->asDate($model[$option]) : null;
                break;
            case 'student_id' :
                return sprintf('%06d', $model[$option]);
                break;
            case 'status' :
                return Studyplan::getStatusValue($model[$option]);
                break;
            case 'status_reason' :
                return Studyplan::getStatusReasonValue($model[$option]);
                break;
            case 'student_birth_age' :
                $age = \artsoft\helpers\ArtHelper::age($model['student_birth_date']);
                return $age['age_year'] . ' лет ' . $age['age_month'] . ' мес.';
                break;
            case 'student_gender' :
            case 'signer_gender' :
                return UserCommon::getGenderValue($model[$option]);
                break;
            case 'student_sert_name' :
                return Student::getDocumentValue($model[$option]);
                break;
            case 'student_sert_doc' :
                return $model['student_sert_series'] ? 'серия ' . $model['student_sert_series'] . ' №' . $model['student_sert_num'] . ' выдан ' . $model['student_sert_organ'] . ' ' . Yii::$app->formatter->asDate($model['student_sert_date']) : '';
                break;
            case 'limited_status_list' :
                $limit = [];
                foreach (explode(',', $model[$option]) as $i => $st) {
                    $limit[] = Student::getLimitedStatusValue($st);
                }
                return implode(',', $limit);
                break;
            case 'signer_sert_name' :
                return Parents::getDocumentValue($model[$option]);
                break;
            case 'signer_doc' :
                return $model['signer_sert_series'] ? 'серия ' . $model['signer_sert_series'] . ' №' . $model['signer_sert_num'] . ' выдан ' . $model['signer_sert_organ'] . ' ' . Yii::$app->formatter->asDate($model['signer_sert_date']) . ' код ' . $model['signer_sert_code'] : '';
                break;
            case 'plan_year' :
                return $model[$option] . '/' . ($model[$option] + 1);
                break;
            case 'student_social_card_flag' :
            case 'signer_social_card_flag' :
                return $model[$option] == 1 ? 'Да' : ($model[$option] == 2 ? 'Нет' : 'Не задано');
                break;
            default :
                return $model[$option];
        }
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
