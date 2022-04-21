<?php

use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['/teachers/default/efficiency', 'id' => $id]];
$this->params['breadcrumbs'][] = 'График';
?>

<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">
            График эффективности
        </div>
        <div class="panel-body">
            <?= $this->render('_plan_year-search', compact('model_date')) ?>
            <div class="panel">
                <div class="panel-body">

                    <?= ChartJs::widget([
                        'type' => 'bar',
                        'clientOptions' => [
                            'scales' => [
                                'yAxes' => [
                                    [
                                        'ticks' => [
                                            'beginAtZero' => true,
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'data' => [
                            'labels' => array_column($data, 'label'),
                            'datasets' => [
                                [
                                    'label' => "Бонусы по месяцам",
                                    'backgroundColor' => "rgba(255,0,0,0.9)",
                                    'data' => array_column($data, 'bonus'),
                                ]
                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

