<?php

use dosamigos\chartjs\ChartJs;
?>
<div class="panel panel-default dw-widget">
    <div class="panel-heading"><?= Yii::t('art/user', 'Users Browser') ?></div>
    <div class="panel-body">
        <?=
        ChartJs::widget([
            'type' => 'doughnut',
            'options' => [
            // 'height' => 200,
            ],
            'clientOptions' => [
                'legend' => [
                    'display' => true,
                    'position' => 'left',
                    'labels' => [
                        'fontSize' => 14,
                        'fontColor' => "#425062",
                    ]
                ],
                'maintainAspectRatio' => false,
            ],
            'data' => [
                'radius' => "90%",
                'labels' => $labels,
                'datasets' => [
                    [
                        'backgroundColor' => $backgroundColor,
                        'data' => $data,
                    ],
                ]
            ]
        ]);
        ?>

    </div>
</div>
