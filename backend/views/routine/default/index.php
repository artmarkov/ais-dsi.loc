<?php

use tecnocen\yearcalendar\widgets\ActiveCalendar;
use yii\data\ActiveDataProvider;


$JSRange = <<<EOF
        function(e) {
        
         var start = new Date(e.startDate),
         end = new Date(e.endDate);
         
         $.ajax({
            url: '/admin/routine/default/init-event',
            type: 'POST',
            data: {
            startDate: (('0'+(start.getDate())).slice(-2)) +'/'+ (('0'+(start.getMonth()+1)).slice(-2)) +'/'+ start.getFullYear(), 
            endDate: (('0'+(end.getDate())).slice(-2)) +'/'+ (('0'+(end.getMonth()+1)).slice(-2)) +'/'+ end.getFullYear()},
            success: function (res) {
            showDay(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
}
EOF;

$JSOnDay = <<<EOF
        function(e) {
        console.log(e.date);
}
EOF;

$JSOutDay = <<<EOF
        function(e) {
        console.log(e.date);
}
EOF;

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
                                'alwaysHalfDay' => true,
                                'disabledDays' => [],
                            ],
                            'clientEvents' => [
                                'selectRange' => new \yii\web\JsExpression($JSRange),
                                'mouseOnDay' => new \yii\web\JsExpression($JSOnDay),
                                'mouseOutDay' => new \yii\web\JsExpression($JSOutDay),
                            ]
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php \yii\bootstrap\Modal::begin([
    'header' => '<h3 class="lte-hide-title page-title">' . Yii::t('art/calendar', 'Event') . '</h3>',
    'size' => 'modal-lg',
    'id' => 'routine-modal',
    'footer' => 'footer',
]);

\yii\bootstrap\Modal::end(); ?>

<?php
$js = <<<JS

function showDay(res) {

    $('#routine-modal .modal-body').html(res);
    $('#routine-modal').modal();
}
JS;

$this->registerJs($js);
?>