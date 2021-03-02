<?php

use tecnocen\yearcalendar\widgets\ActiveCalendar;


$JSRange = <<<EOF
        function(e) {
         console.log("Select a range: " + e.startDate.toLocaleDateString() + " - " + e.endDate.toLocaleDateString());
        
         $.ajax({
            url: '/admin/routine/default/init-event',
            type: 'POST',
            data: {
                startDate: e.startDate.toLocaleDateString(), 
                endDate: e.endDate.toLocaleDateString()
            },
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
        <!--console.log(e.events);-->
        <!--console.log(e.date);-->
        if(e.events.length > 0) {
                var content = '';
                
                for(var i in e.events) {
                <!--console.log(e.events[i].color);-->
                    content += '<div class="event-tooltip-content">'
                                    + '<div class="event-name" style="color:' + e.events[i].color + '">' + e.events[i].name + '</div>'
                                    + '<div class="event-location">' + e.events[i].location + '</div>'
                                + '</div>';
                }
            
                $(e.element).popover({
                    trigger: 'manual',
                    container: 'body',
                    html:true,
                    content: content
                });
                
                $(e.element).popover('show');
            }
}
EOF;

$JSOutDay = <<<EOF
        function(e) {
//        console.log(e.date);
        if(e.events.length > 0) {
                $(e.element).popover('hide');
            }
}
EOF;
$JSClick = <<<EOF
        function(e) {
        console.log("Click on day: " + e.date.toLocaleDateString() + " (" + e.events.length + " events)")
        
}
EOF;

// подсветка текущего дня и выходных
$JSCast = <<<EOF
        function(element, date) {
           var currentYear = new Date().getFullYear();
           var currentMonth = new Date().getMonth();
           var currentDay = new Date().getDay();
           var currentDateTime = new Date(currentYear, currentMonth, currentDay).getTime();
           var currentWeekDay = date.getDay();
           
            if(date.getTime() == currentDateTime) {
                $(element).css('border', '2px solid blue');
            }
            if(0 == currentWeekDay) {
                $(element).css('color', 'red');
            }
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
                        <?= ActiveCalendar::widget([
                            'id' => 'calendar',
                            'language' => Yii::$app->language,
                            'dataProvider' => $dataProvider,
                            'options' => [
                                // HTML attributes for the container.
                                // the `tag` option is specially handled as the HTML tag name
                            ],
                            'clientOptions' => [

                                'customDayRenderer' => new \yii\web\JsExpression($JSCast),
                                'enableContextMenu' => true,
                                'enableRangeSelection' => true,
                                'displayWeekNumber' => false,
                                'alwaysHalfDay' => false,
                                'disabledDays' => [],
                            ],
                            'clientEvents' => [
                                'clickDay' => new \yii\web\JsExpression($JSClick),
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
<?php $this->registerCss('
	.col {
		color: red;
		
	}
');
?>