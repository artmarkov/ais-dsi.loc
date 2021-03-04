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
    function(info) {
    console.log('Клик по событию: ' + info.event.title);
    console.log('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
    console.log('View: ' + info.view.type);

    // change the border color just for fun
    info.el.style.borderColor = 'red';
//        var eventData;
//            eventData = {
//                id: calEvent.id,
//            };
//        console.log('кликаем по событию');
//        console.log(eventData);
//      $.ajax({
//            url: '/admin/calendar/event/init-event',
//            type: 'POST',
//            data: {eventData : eventData},
//            success: function (res) {
//               // console.log(res);
//            showDay(res);
//            },
//            error: function () {
//                alert('Error!!!');
//            }
//        });
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
            ?>

            <?= \backend\widgets\fullcalendar\src\Fullcalendar::widget([
                'headerToolbar' => [
                    'left' => 'prev,next today',
                    'center' => 'title',
                    'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',

                ],
                'clientOptions' => [
                    'locale' => 'ru',
                    'initialDate' => '2020-09-12',
                    'initialView' => 'timeGridWeek',
//                    'height' => 'auto',
                    'expandRows' => true,
                    'slotMinTime' => '08:00',
                    'slotMaxTime' => '20:00',
                    'selectable' => true,
                    'droppable' => true,
                    'editable' => true,
                    'eventDurationEditable' => true, // разрешить изменение размера
                    'eventOverlap' => true, // разрешить перекрытие событий
//                    'eventClick' => new JsExpression($JSEventClick),
//                    'eventMouseEnter' => new JsExpression("function(info) {console.log('Навел мышкой на: ' + info.event.title);}"),
//                    'eventMouseLeave' => new JsExpression("function(info) {console.log('Снял мышку с: ' + info.event.title);}"),
//                    'eventDrop' => new JsExpression("function(info) {console.log('Drop: ' + info.event.title);}"),
//                    'eventResize' => new JsExpression("function(info) {console.log('Resize: ' + info.event.title);}"),
                    'select' => new JsExpression("function(info) {console.log('selected ' + info.startStr + ' to ' + info.endStr);}"),
//                    'select' => new JsExpression($JSSelect),
//                    'eventResize' => new JsExpression($JSEventResize),
//                    'eventDrop' => new JsExpression($JSEventDrop),
                    'defaultTimedEventDuration' => '00:45:00', // при перетаскивании события в календарь задается длительность события
                    'defaultAllDayEventDuration' => [
                        'days' => '1'// то-же при перетаскиваниив в allDay
                    ],
                    'aspectRatio' => 1.8,
                    'navLinks' => true,
                ],
                'events' => [
                    [
                        'title' => 'Business Lunch',
                        'start' => '2020-09-04T13:00:00',
                        // 'constraint' => 'businessHours'
                    ],
                ]
//               'events' => \yii\helpers\Url::to(['/calendar/event/calendar']),
            ]);
            ?>
            <?php $this->registerCss('
	
	#external-events {
		float: left;
		padding: 0 10px;
		text-align: left;
	}
	
	#external-events .fc-event {
		margin: 10px 0;
		cursor: pointer;
	}	
');
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

<?php
$js = <<<JS

function showDay(res) {
    $('#event-modal .modal-body').html(res);
    $('#event-modal').modal();
}
JS;

$this->registerJs($js);
?>

