<?php

use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => 'Журнал посещаемости', 'url' => ['/reports/working-time/index']];
$this->params['breadcrumbs'][] = 'График';
?>

<div class="working-time-bar">
    <div class="panel">
        <div class="panel-heading">
            График
        </div>
        <div class="panel-body">
            <?= $this->render('_search_stat', compact('model_date')) ?>
            <div class="panel">
                <div class="panel-body">
                <?php
                usort($data['data'], function ($a, $b) {
                    return $b['total_in'] <=> $a['total_in'];
                });
                ?>
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
                            'labels' => array_column($data['data'], 'name'),
                            'datasets' => [
                                [
                                    'label' => $data['attributes']['total_in'],
                                    'backgroundColor' => "rgba(255, 0, 0,0.9)",
                                    'data' => array_column($data['data'], 'total_in'),
                                ],
                                [
                                    'label' => $data['attributes']['total_out'],
                                    'backgroundColor' => "rgba(255, 128, 0,0.9)",
                                    'data' => array_column($data['data'], 'total_out'),
                                ]
                            ]
                        ]
                    ]);
                    ?>
                <?php
                usort($data['data'], function ($a, $b) {
                    return $b['total_truancy'] <=> $a['total_truancy'];
                });
                ?>
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
                            'labels' => array_column($data['data'], 'name'),
                            'datasets' => [
                                [
                                    'label' => $data['attributes']['total_truancy'],
                                    'backgroundColor' => "rgba(255, 0, 0,0.9)",
                                    'data' => array_column($data['data'], 'total_truancy'),
                                ],

                                [
                                    'label' => $data['attributes']['total_reserv'],
                                    'backgroundColor' => "rgba(0, 128, 255, 0.9)",
                                    'data' => array_column($data['data'], 'total_reserv'),
                                ],
                                [
                                    'label' => $data['attributes']['total_exit'],
                                    'backgroundColor' => "rgba(0, 0, 255,0.9)",
                                    'data' => array_column($data['data'], 'total_exit'),
                                ],

                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

