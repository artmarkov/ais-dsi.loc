<?php

use tecnocen\yearcalendar\widgets\ActiveCalendar;
use yii\data\ActiveDataProvider;
?>
<div class="department-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo ActiveCalendar::widget([
                        'language' => Yii::$app->language,
                        'dataProvider' => new ActiveDataProvider([
                            'query' => \common\models\calendar\Conference::find()
                        ]),
                        'options' => [
                            // HTML attributes for the container.
                            // the `tag` option is specially handled as the HTML tag name
                        ],
                        'clientOptions' => [
                            'enableContextMenu' => true,
                            'enableRangeSelection' => true,
                            'displayWeekNumber' => false,
                            'alwaysHalfDay' =>true,
                            'disabledDays'=> [],
//                            'startYear'=> '2018',
//                            'minDate'=> new \yii\web\JsExpression('new Date("2015-01-01")'),
//                            'maxDate'=> new \yii\web\JsExpression('new Date("2025-12-31")'),

                            // JS Options to be passed to the `calendar()` plugin.
                            // see http://bootstrap-year-calendar.com/#Documentation/Options
                            // The `dataSource` property will be overwritten by the dataProvider.
                        ],
                        'clientEvents' => [
                            'mouseOnDay' => '',
                            // JS Events for the `calendar()` plugin.
                            // see http://bootstrap-year-calendar.com/#Documentation/Events
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
