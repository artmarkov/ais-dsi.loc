<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use common\models\schoolplan\SchoolplanView;
use yii\helpers\ArrayHelper;

class SchoolplanReport
{
    protected $date_in;
    protected $date_out;
    protected $category_list;

    public function __construct($model_date)
    {
        $timestamp = ArtHelper::getMonYearParamsFromArray([$model_date->date_in, $model_date->date_out]);
        $this->date_in = $timestamp[0];
        $this->date_out = $timestamp[1];
        $this->category_list = \common\models\guidesys\GuidePlanTree::getPlanList();
//        echo '<pre>' . print_r($this->category_list, true) . '</pre>';
//        die();
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getData()
    {
        $models = SchoolplanView::find()
            ->select(new \yii\db\Expression("category_id, COUNT(id) AS count_plan, COUNT(num_users) AS count_users, COUNT(num_winners) AS count_winners, COUNT(num_visitors) AS count_visitors"))
            ->where(['between', 'datetime_in', $this->date_in, $this->date_out])
            ->asArray()
            ->groupBy('category_id')
            ->all();
        $models = ArrayHelper::index($models, 'category_id');
        $attributes = [
            'category_id' => 'Категория',
            'count_plan' => 'Кол-во мероприятий',
            'count_users' => 'Количество участников',
            'count_winners' => 'Количество победителей',
            'count_visitors' => 'Количество зрителей',
        ];

        $data = [];
        $all_summ = ['count_plan' => 0, 'count_users' => 0, 'count_winners' => 0, 'count_visitors' => 0];
        $i = 0;
        foreach ($this->category_list as $category_id => $name) {
            if(isset($models[$category_id])) {
                $data[$i]['category_id'] = $name;
                $data[$i]['count_plan'] = $models[$category_id]['count_plan'];
                $data[$i]['count_users'] = $models[$category_id]['count_users'];
                $data[$i]['count_winners'] = $models[$category_id]['count_winners'];
                $data[$i]['count_visitors'] = $models[$category_id]['count_visitors'];
                $all_summ['count_plan'] += $data[$i]['count_plan'];
                $all_summ['count_users'] += $data[$i]['count_users'];
                $all_summ['count_winners'] += $data[$i]['count_winners'];
                $all_summ['count_visitors'] += $data[$i]['count_visitors'];

                $i++;
            }
        }
        return ['data' => $data, 'all_summ' => $all_summ, 'category_list' => $this->category_list, 'attributes' => $attributes];
    }
}
