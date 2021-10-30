<?php

namespace common\widgets;

use common\models\efficiency\TeachersEfficiency;
use yii\base\InvalidConfigException;
use Yii;

class EfficiencyUserBarWidget extends \yii\base\Widget
{

    public $timestamp_in = null;
    public $timestamp_out = null;
    public $id;

    public function run()
    {
        $this->id = $this->id ?: Yii::$app->request->get('id');
        if (!$this->setDate() && !$this->id) {
            throw new InvalidConfigException("The 'date_in', 'date_out', 'id' can not null.");
        }
        $total = [];
        $data = TeachersEfficiency::find()
            ->select(['date_in', 'bonus'])
            ->where(['between', 'date_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['=', 'teachers_id', $this->id])
            ->orderBy('date_in')
            ->asArray()
            ->all();

        foreach ($data as $item => $val) {
            $date = Yii::$app->formatter->asDate($val['date_in'], 'php:d.m.Y');
            $total[$date] = isset($total[$val['date_in']]) ? $total[$date] + $val['bonus'] : $val['bonus'];
        }

        return $this->render('efficiencyUserBar', [
            'labels' => array_keys($total),
            'data' => array_values($total),
        ]);
    }

    /**
     * @return bool
     */
    protected function setDate()
    {
        $day_in = Yii::$app->settings->get('module.day_in', 21);
        $day_out = Yii::$app->settings->get('module.day_out', 20);

        $d = date('d');
        $m = date('m');
        $y = date('Y');

        $mon = $d > $day_in ? ($m == 12 ? 1 : $m + 1) : $m;
        $year = $m == 12 ? $y + 1 : $y;

        $this->timestamp_in = $this->timestamp_in != null ? $this->timestamp_in : Yii::$app->formatter->asTimestamp(mktime(0, 0, 0, ($mon - 1), $day_in, $year), 'php:d.m.Y');
        $this->timestamp_out = $this->timestamp_out != null ? $this->timestamp_out : Yii::$app->formatter->asTimestamp(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
        return $this->timestamp_in && $this->timestamp_out ? true : false;
    }

}
