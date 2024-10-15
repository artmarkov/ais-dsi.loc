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
            '7' => [['Фортепиано'],[]],
            '9' => [['Баян'],[]],
            '10' => [['Аккордеон'],[]],
            '11' => [['Домра'],[]],
            '12' => [['Балалайка'],[]],
            '13' => [['Гитара'],[]],
            '14' => [['Гусли'],[]],
            '15' => [['Гармонь'],[]],
            '17' => [['Флейта'],[]],
            '18' => [['Гобой'],[]],
            '19' => [['Кларнет'],[]],
            '20' => [['Фагот'],[]],
            '21' => [['Саксофон'],[]],
            '22' => [['Труба'],[]],
            '23' => [['Валторна'],[]],
            '24' => [['Трамбон', 'Баритон'],[]],
            '25' => [['Туба'],[]],
            '26' => [['Ударные'],[]],
            '28' => [['Скрипка'],[]],
            '29' => [['Виолончель'],[]],
            '30' => [['Альт'],[]],
            '31' => [['Контрабас'],[]],
            '32' => [['Арфа'],[]],
            '33' => [['Ударные','Саксофон','Бас-гитара','Тромбон','Труба'],[1011,1012,1024,1025]],
            '34' => [['Хор'],[]],
            '35' => [['Фольклорный ансамбль'],[]],
            '36' => [[''],[1013,1034]],
            '37' => [[],[]],
            '38' => [[''],[1044]],
            '39' => [[''],[1051,1052]],
            '40' => [[],[]],
            '41' => [[''],[1046,1047,1048,1049]],
            '42' => [[],[]],
            '43' => [[''],[1033]],
            '44' => [[],[]],
            '46' => [[],[]],
            '47' => [['Электрогитара'],[]],
            '48' => [['Эстрадный вокал'],[]],
            '49' => [['Академический вокал'],[]],
            '50' => [[],[]],
            '51' => [[],[]],
            '52' => [[],[]],
        ];
        $st = $this->getStudyplans();
        $spec = ArrayHelper::index($st, null, ['speciality']);
        foreach ($mask as $index => $array) {
            $data[$index . '_all'] = 0;
            if (!empty($array[0])) {
                foreach ($array[0] as $value) {
                    if (isset($spec[$value])) {
                        if (empty($array[1])) {
                            $data[$index . '_all'] += count($spec[$value]);
                        } else {
                            foreach ($spec[$value] as $val) {
                                $data[$index . '_all'] += in_array($val['programm_id'], $array[1]) ? 1 : 0;
                            }
                        }
                    }
                }
            }
        }

       // echo '<pre>' . print_r($spec, true) . '</pre>';
      //  echo '<pre>' . print_r($data, true) . '</pre>';
        //echo '<pre>' . print_r($spec, true) . '</pre>';
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
