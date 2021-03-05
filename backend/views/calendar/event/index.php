<?php

use artsoft\helpers\Html;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//echo '<pre>' . print_r($events, true) . '</pre>';

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="col-sm-12"

            <?php
            // выбираем мышкой область или кликаем в пустое поле
            $JSSelect = <<<EOF
    function(start, end, jsEvent, view) {
        var eventData;
            eventData = {
                id: 0,
                start: start.format(),
                end: end.format(),
                allDay: start.allDay,
            };
        $('#w0').fullCalendar('renderEvent', eventData, true);
        console.log('выбираем мышкой область или кликаем в пустое поле');
        console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/init-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
    //            console.log(res);
    //        $('#w0').fullCalendar('renderEvent', eventData, true);
            showDay(res);
            },
            error: function () {
                $('#w0').fullCalendar('unselect');
            }
        });
    }
EOF;
            // кликаем по событию
            $JSEventClick = <<<EOF
    function(e) {

    // change the border color just for fun
    e.el.style.borderColor = 'red';
        
        console.log('кликаем по событию ' + e.event.id);
      $.ajax({
            url: '/admin/calendar/event/init-event',
            type: 'POST',
            data: {id: e.event.id},
            success: function (res) {
//                console.log(res);
                $('#event-modal .modal-body').html(res);
                $('#event-modal').modal();
            },
            error: function () {
                alert('Error!!!');
            }
        });
    }
EOF;
            // бросаем событие извне
            $JSDrop = <<<EOF
    function(date, jsEvent, ui, resourceId ) {
      if ($('#drop-remove').is(':checked')) {
             $(this).remove();
             }
        var eventData;
            eventData = {
                id: 0,
                title: ui.helper[0].innerHTML,
                start: date.format(),
                end: date.format(),
            };
        console.log('бросаем событие извне');
        console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
                  console.log(res);
            },
            error: function () {
               // alert('Error!!!');
               console.log(res);
            }
        });
    }
EOF;
            // растягиваем/сжимаем событие мышкой
            $JSEventResize = <<<EOF
    function(event, delta, revertFunc, jsEvent, ui, view) {
      var eventData;
            eventData = {
                id: event.id,
                title: event.title,
                start: event.start.format(),
                end: event.end.format(),
                allDay: event.allDay,
            };
        console.log('растягиваем/сжимаем событие мышкой');
        console.log(eventData);
         $.ajax({
            url: '/admin/calendar/event/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
                //  console.log(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
      }
EOF;
            // перетаскиваем событие, удерживая мышкой
            $JSEventDrop = <<<EOF
    function(event, delta, revertFunc, jsEvent, ui, view) {
        var eventData;
            eventData = {
                id: event.id,
                title: event.title,
                start: event.start.format(),
                end: event.end == null ? null : event.end.format(),
                allDay: event.allDay,
            };
      
     console.log('перетаскиваем событие, удерживая мышкой');
     console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
                //  console.log(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
      }
EOF;
            $JSOnDay = <<<EOF
        function(info) {
        console.log(info.event.start);
                var content = '<div class="event-tooltip-content">'
                                    + '<div class="event-name">' + info.event.title + '</div>'
                                + '</div>';
            
                $(info.el).popover({
                    trigger: 'manual',
                    container: 'body',
                    html:true,
                    content: content
                });
                
                $(info.el).popover('show');
}
EOF;
            $JSOutDay = <<<EOF
        function(info) {
                $(info.el).popover('hide');
}
EOF;
            ?>

            <?= \backend\widgets\fullcalendar\src\Fullcalendar::widget([
                'clientOptions' => [
//                    'initialDate' => '2020-09-12',
                    'initialView' => 'timeGridWeek',
                    'height' => 'auto', // 'auto' - aspectRatio no works
                    'aspectRatio' => 1.8,
                    'navLinks' => true,
                    'businessHours' => true,
                    'editable' => true,
                    'selectable' => true,
                    'expandRows' => true,
                    'slotMinTime' => '07:00',
                    'slotMaxTime' => '22:00',
                    'eventDurationEditable' => true, // разрешить изменение размера
                    'eventOverlap' => true, // разрешить перекрытие событий
                    'eventClick' => new JsExpression($JSEventClick),
                    'eventMouseEnter' => new JsExpression($JSOnDay),
                    'eventMouseLeave' => new JsExpression($JSOutDay),
                    'eventDrop' => new JsExpression("function(info) {console.log('Drop: ' + info.event.title);}"),
                    'eventResize' => new JsExpression("function(info) {console.log('Resize: ' + info.event.title);}"),
                    'select' => new JsExpression("function(info) {console.log('selected ' + info.startStr + ' to ' + info.endStr);}"),
//                    'select' => new JsExpression($JSSelect),
//                    'eventResize' => new JsExpression($JSEventResize),
//                    'eventDrop' => new JsExpression($JSEventDrop),
                ],
               'events' => \yii\helpers\Url::to(['/calendar/event/calendar']),
            ]);
            ?>
        </div>
    </div>
</div>

<?php \yii\bootstrap\Modal::begin([
    'header' => '<h3 class="lte-hide-title page-title">' . Yii::t('art/calendar', 'Event') . '</h3>',
    'size' => 'modal-lg',
    'id' => 'event-modal',
    //'footer' => 'footer',
]);

\yii\bootstrap\Modal::end(); ?>


