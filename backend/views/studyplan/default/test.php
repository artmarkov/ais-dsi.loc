<?php

use common\widgets\weeklyscheduler\WeeklyScheduler;
use yii\web\JsExpression;
$JSInit = <<<EOF
    function(e,f) {
    console.log(e);
    console.log(f);
    }
EOF;
?>
<div class="panel panel-info">
    <div class="panel-heading">
        График
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?= WeeklyScheduler::widget([
                    'options' => ['class' => "jqs-demo mb-3"],
                    'mode' => 'edit',
                    'data' => [
                        [
                            'day' => 0,
                            'id' => '123456789',
                            'periods' => [
                                [
                                    'id' => '123456789',
                                    'start' => '10:00',
                                    'end' => '12:15',
                                    'title' => '1 period',
                                    'backgroundColor' => 'rgba(0, 0, 0, 0.7)',
                                    'borderColor' => '#000',
                                    'textColor' => '#fff'
                                ]
                            ]
                        ],
                        [
                            'day' => 1,
                            'periods' => [
                                [
                                    'start' => '12:00',
                                    'end' => '15:00',
                                    'title' => '2 period',
                                    'backgroundColor' => 'rgba(0, 0, 255, 0.9)',
                                    'borderColor' => '#000',
                                    'textColor' => '#fff'
                                ]
                            ]
                        ],
                    ],
                    'events' => [
                        'onInit' => new JsExpression($JSInit),
                        'onAddPeriod' => new JsExpression($JSInit),
                        'onRemovePeriod' => new JsExpression($JSInit),
                        'onDuplicatePeriod' => new JsExpression($JSInit),
                        'onClickPeriod' => new JsExpression($JSInit),
                    ]

                ]);
                ?>
            </div>
        </div>
    </div>
</div>