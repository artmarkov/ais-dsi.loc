<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class StudyplanDistrib
{
    const template = 'document/report_form_distrib.xlsx';

    protected $plan_year;
    protected $template_name;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->template_name = self::template;
    }

    protected function getStudyplans()
    {
        $models = (new Query())->from('studyplan_stat_view')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->all();
        return $models;
    }

    protected function getStudyplansOld()
    {
        $models = (new Query())->from('studyplan_stat_view')
            ->where(['plan_year' => $this->plan_year - 1])
            ->andWhere(['AND',
                ['status' => Studyplan::STATUS_INACTIVE],
                ['status_reason' => 4]
            ])
            ->all();
        return $models;
    }
    protected function getEntrants()
    {
        $models = (new Query())->from('entrant')
            ->select(['entrant.programm_id', 'guide_education_cat.programm_short_name', 'subject.name as speciality', 'entrant.subject_form_id'])
            ->innerJoin('entrant_comm', 'entrant.comm_id = entrant_comm.id')
            ->leftJoin('subject', 'entrant.subject_id = subject.id')
            ->innerJoin( 'education_programm' , 'education_programm.id = entrant.programm_id')
            ->innerJoin( 'guide_education_cat','guide_education_cat.id = education_programm.education_cat_id')
            ->where(['entrant_comm.plan_year' => $this->plan_year])
            ->andWhere(['entrant.course' => 1])
            ->all();
        return $models;
    }

    public function getData()
    {
        $data = [];
        $data['rank'] = 'doc';
        $mask = [
            '7' => [['Фортепиано'], []],
            '9' => [['Баян'], []],
            '10' => [['Аккордеон'], []],
            '11' => [['Домра'], []],
            '12' => [['Балалайка'], []],
            '13' => [['Гитара'], []],
            '14' => [['Гусли'], []],
            '15' => [['Гармонь'], []],
            '17' => [['Флейта'], []],
            '18' => [['Гобой'], []],
            '19' => [['Кларнет'], []],
            '20' => [['Фагот'], []],
            '21' => [['Саксофон'], []],
            '22' => [['Труба'], []],
            '23' => [['Валторна'], []],
            '24' => [['Трамбон', 'Баритон'], []],
            '25' => [['Туба'], []],
            '26' => [['Ударные'], []],
            '28' => [['Скрипка'], []],
            '29' => [['Виолончель'], []],
            '30' => [['Альт'], []],
            '31' => [['Контрабас'], []],
            '32' => [['Арфа'], []],
            '33' => [['Ударные', 'Саксофон', 'Бас-гитара', 'Тромбон', 'Труба'], [1011, 1012, 1024, 1025]],
            '34' => [['Хор'], []],
            '35' => [['Фольклорный ансамбль'], []],
            '36' => [[''], [1013, 1034]],
            '37' => [[], []],
            '38' => [[''], [1044]],
            '39' => [[''], [1051, 1052]],
            '40' => [[], []],
            '41' => [[''], [1046, 1047, 1048, 1049]],
            '42' => [[], []],
            '43' => [[''], [1033]],
            '44' => [[], []],
            '46' => [[], []],
            '47' => [['Электрогитара'], []],
            '48' => [['Эстрадный вокал'], []],
            '49' => [['Академический вокал'], []],
            '50' => [[], []],
            '51' => [[], []],
            '52' => [[], []],
        ];
        $st = $this->getStudyplans();
        $ent = $this->getEntrants();
        $sto = $this->getStudyplansOld();

//        echo '<pre>' . print_r($sto, true) . '</pre>';
//        die();
        $st_lim = array_filter(
            $st,
            function ($item) {
                return array_intersect(explode(',', $item['limited_status_list']), [1000, 2000]); // С ограничениями по здоровью
            }
        );
        $st_pp = array_filter(
            $st,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ПП'; // ПП Общая
            }
        );
        $st_ppb = array_filter(
            $st_pp,
            function ($item) {
                return $item['subject_form_id'] != 1001; // ПП Бюджет
            }
        );
        $st_op = array_filter(
            $st,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ОП'; // ОП Общая
            }
        );
        $st_opb = array_filter(
            $st_op,
            function ($item) {
                return $item['subject_form_id'] != 1001; // ОП Бюджет
            }
        );
        $ent_pp = array_filter(
            $ent,
            function ($item) {
                return $item['programm_short_name'] == 'ПП'; // ПП Общая
            }
        );
        $ent_ppb = array_filter(
            $ent_pp,
            function ($item) {
                return $item['subject_form_id'] != 1001; // ПП Бюджет
            }
        );
        $ent_pph = array_filter(
            $ent_pp,
            function ($item) {
                return $item['subject_form_id'] == 1001; // ПП Хозрасчет
            }
        );
        $ent_op = array_filter(
            $ent,
            function ($item) {
                return $item['programm_short_name'] == 'ОП'; // ОП Общая
            }
        );
        $ent_opb = array_filter(
            $ent_op,
            function ($item) {
                return $item['subject_form_id'] != 1001; // ОП Бюджет
            }
        );
        $ent_oph = array_filter(
            $ent_op,
            function ($item) {
                return $item['subject_form_id'] == 1001; // ОП Хозрасчет
            }
        );
        $sto_lim = array_filter(
            $sto,
            function ($item) {
                return array_intersect(explode(',', $item['limited_status_list']), [1000, 2000]); // С ограничениями по здоровью
            }
        );
        $sto_pp = array_filter(
            $sto,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ПП'; // ПП Общая
            }
        );
        $sto_pp_lim = array_filter(
            $sto_lim,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ПП'; // ПП Общая с ограничениями
            }
        );
        $sto_op = array_filter(
            $sto,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ОП'; // ОП Общая
            }
        );
        $sto_op_lim = array_filter(
            $sto_lim,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ОП'; // ОП Общая с ограничениями
            }
        );
//        echo '<pre>' . print_r($st_lim, true) . '</pre>';
//        die();
        foreach ($mask as $index => $array) {
            $data[$index . '_all'] = $data[$index . '_lim'] = $data[$index . '_pp'] = 0;
            $data[$index . '_ppb'] = $data[$index . '_op'] = $data[$index . '_opb'] = 0;
            $data[$index . '_eppb'] = $data[$index . '_epph'] = $data[$index . '_eopb'] = $data[$index . '_eoph'] = 0;
            $data[$index . '_ppall'] = $data[$index . '_pplim'] = $data[$index . '_opall'] = $data[$index . '_oplim'] = 0;
            if (!empty($array[0])) {
                foreach ($array[0] as $value) {
                    $data[$index . '_all'] = $this->setColl($data[$index . '_all'], $array, $st, $value);
                    $data[$index . '_lim'] = $this->setColl($data[$index . '_lim'], $array, $st_lim, $value);
                    $data[$index . '_pp'] = $this->setColl($data[$index . '_pp'], $array, $st_pp, $value);
                    $data[$index . '_ppb'] = $this->setColl($data[$index . '_ppb'], $array, $st_ppb, $value);
                    $data[$index . '_op'] = $this->setColl($data[$index . '_op'], $array, $st_op, $value);
                    $data[$index . '_opb'] = $this->setColl($data[$index . '_opb'], $array, $st_opb, $value);
                    $data[$index . '_eppb'] = $this->setColl($data[$index . '_eppb'], $array, $ent_ppb, $value);
                    $data[$index . '_epph'] = $this->setColl($data[$index . '_epph'], $array, $ent_pph, $value);
                    $data[$index . '_eopb'] = $this->setColl($data[$index . '_eopb'], $array, $ent_opb, $value);
                    $data[$index . '_eoph'] = $this->setColl($data[$index . '_eoph'], $array, $ent_oph, $value);
                    $data[$index . '_ppall'] = $this->setColl($data[$index . '_ppall'], $array, $sto_pp, $value);
                    $data[$index . '_pplim'] = $this->setColl($data[$index . '_pplim'], $array, $sto_pp_lim, $value);
                    $data[$index . '_opall'] = $this->setColl($data[$index . '_opall'], $array, $sto_op, $value);
                    $data[$index . '_oplim'] = $this->setColl($data[$index . '_oplim'], $array, $sto_op_lim, $value);
                }
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @param $array
     * @param $st
     * @param $value
     * @return int
     */
    protected function setColl($data, $array, $st, $value)
    {
        $spec = ArrayHelper::index($st, null, ['speciality']);
        if (isset($spec[$value])) {
            if (empty($array[1])) {
                $data += count($spec[$value]);
            } else {
                foreach ($spec[$value] as $val) {
                    $data += in_array($val['programm_id'], $array[1]) ? 1 : 0;
                }
            }
        }
        return $data;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = $this->getData();
        $output_file_name = Yii::$app->formatter->asDate(time(), 'php:Y-m-d_H-i-s') . '_' . basename($this->template_name);
        $tbs = DocTemplate::get($this->template_name)->setHandler(function ($tbs) use ($data) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
