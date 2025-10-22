<?php

namespace common\models\reports;

use artsoft\helpers\ExcelObjectList;
use artsoft\helpers\RefBook;
use common\models\own\Department;
use Yii;
use yii\db\Query;

class CreativeStat
{
    protected $options;

    const OPTIONS_FIELDS = [
        'teachers_list' => 'ФИО преподавателя',
        'department_list' => 'Отдел',
        'name' => 'Название работы',
        'description' => 'Описание работы',
        'published_at' => 'Опубликовано',
        'place' => 'Название учреждения',
        'date' => 'Дата получения сертификата',
        'status' => 'Статус',

    ];

    const OPTIONS_FIELDS_DEFAULT = ['teachers_list', 'department_list', 'place', 'date', 'status'];

    public function __construct($model_date)
    {
        $this->options = $model_date->options;
        if (empty($this->options)) {
            throw new \Error(
                'Выберите хотя бы одну опцию!'
            );
        }
    }

    protected function getCreatives()
    {
        $models = (new Query())->from('creative_works')
            ->where(['category_id' => 1002])
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
        foreach ($this->getCreatives() as $id => $model) {
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
        $teachers_list = RefBook::find('teachers_fullname')->getList();
        $department_list =  \yii\helpers\ArrayHelper::map(Department::find()->select('id, name')->asArray()->all(), 'id', 'name');

        switch ($option) {
            case 'published_at' :
            case 'date' :
                return $model[$option] ? Yii::$app->formatter->asDate($model[$option]) : null;
                break;
            case 'student_id' :
                return sprintf('%06d', $model[$option]);
                break;
            case 'teachers_list' :
                $limit = [];
                foreach (explode(',', $model[$option]) as $i => $st) {
                    $limit[] = $teachers_list[$st] ?? '';
                }
                return implode(',', $limit);
                break;
            case 'department_list' :
                $limit = [];
                foreach (explode(',', $model[$option]) as $i => $st) {
                    $limit[] = $department_list[$st] ?? '';
                }
                return implode(',', $limit);
                break;
            case 'status' :
                return $model[$option] == 1 ? 'Выполнено' : 'Запланировано';
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
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_creative-stat.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
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
