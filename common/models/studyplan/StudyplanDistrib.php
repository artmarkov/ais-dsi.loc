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
            $st,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ПП' && $item['subject_form_id'] == 1000; // ПП Бюджет
            }
        );
        $st_op = array_filter(
            $st,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ОП'; // ОП Общая
            }
        );
        $st_opb = array_filter(
            $st,
            function ($item) {
                return $item['education_cat_programm_short_name'] == 'ОП' && $item['subject_form_id'] == 1000; // ОП Бюджет
            }
        );
//        echo '<pre>' . print_r($st_lim, true) . '</pre>';
//        die();
        foreach ($mask as $index => $array) {
            $data[$index . '_all'] = $data[$index . '_lim'] = $data[$index . '_pp'] = 0;
            $data[$index . '_ppb'] = $data[$index . '_op'] = $data[$index . '_opb'] = 0;
            if (!empty($array[0])) {
                foreach ($array[0] as $value) {
                    $data[$index . '_all'] = $this->setColl($data[$index . '_all'], $array, $st, $value);
                    $data[$index . '_lim'] = $this->setColl($data[$index . '_lim'], $array, $st_lim, $value);
                    $data[$index . '_pp'] = $this->setColl($data[$index . '_pp'], $array, $st_pp, $value);
                    $data[$index . '_ppb'] = $this->setColl($data[$index . '_ppb'], $array, $st_ppb, $value);
                    $data[$index . '_op'] = $this->setColl($data[$index . '_op'], $array, $st_op, $value);
                    $data[$index . '_opb'] = $this->setColl($data[$index . '_opb'], $array, $st_opb, $value);
                }
            }
        }
        return $data;
    }

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
