<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\ExcelObjectList;
use artsoft\helpers\RefBook;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TimeReserveStat
{
    protected $plan_year;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
    }

    protected function getSchedules()
    {
        $models = (new Query())->from('subject_schedule_view')
            ->select('auditory_id, week_day, 
            SUM(CASE WHEN time_in >= 39600 AND time_in < 64800 THEN time_out-time_in END) AS sum_14_21,
            ')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['IS NOT', 'subject_schedule_id', NULL])
            ->andWhere(['status' => 1])
            ->andWhere(['direction_id' => 1000])
            ->groupBy('auditory_id, week_day')
            ->all();

        return $models;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getData()
    {
        $data = [];

        $attributes = array_merge(['auditory' => 'Аудитория'], ArtHelper::getWeekdayList());
        array_pop($attributes); // удаляем воскресение

        $auditoryArray = RefBook::find('auditory_memo_1')->getList();
        $dataSchedules = ArrayHelper::index($this->getSchedules(), null, ['auditory_id']);

        foreach ($dataSchedules as $id => $models) {
            $data[$id]['auditory'] = $auditoryArray[$id] ?? $id;

        // Предустановка резерва = 7 часов (с 14 до 21)
            foreach (ArtHelper::getWeekdayList() as $weekday => $weekdayName) {
                $data[$id][$weekday - 1] = 7;
            }
            foreach ($models as $item => $model) {
                $free_time = round((25200 - $model['sum_14_21']) / 3600, 1);
                $data[$id][$model['week_day'] - 1] = $free_time > 0 ? $free_time : 0;
            }
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });
        // echo '<pre>' . print_r($data, true) . '</pre>'; die();
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
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_time-reserve-stat.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
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
