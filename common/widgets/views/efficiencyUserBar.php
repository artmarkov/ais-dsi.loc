<?php

use dosamigos\chartjs\ChartJs;


?>
<div class="teachers-efficiency-bar">
    <div class="panel">

        <div class="panel-body">
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
                            'labels' => $labels,
                            'datasets' => [
                                [
                                    'label' => "Показатели эффективности",
                                    'backgroundColor' => "rgba(255,0,0,0.9)",
                                    'data' => $data,
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