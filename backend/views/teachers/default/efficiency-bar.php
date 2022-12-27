<?php

use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */
/* @var $modelTeachers */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['/teachers/default/efficiency', 'id' => $modelTeachers->id]];
$this->params['breadcrumbs'][] = 'График эффективности';
?>

<div class="teachers-efficiency-summary">
    <div class="panel">
        <div class="panel-heading">
            График эффективности: <?php echo \artsoft\helpers\RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
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

