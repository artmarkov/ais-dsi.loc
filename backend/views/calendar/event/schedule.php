<?php


use artsoft\helpers\Html;
use common\models\calendar\EventCategory;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//echo '<pre>' . print_r($events, true) . '</pre>';

$this->title = 'Events Schedule';
$this->params['breadcrumbs'][] = $this->title;
//
//$DragJS = <<<EOF
//    /* initialize the external events
//    -----------------------------------------------------------------*/
//    $('#external-events .fc-event').each(function() {
//        // store data so the calendar knows to render an event upon drop
//        $(this).data('event', {
//            title: $.trim($(this).text()), // use the element's text as the event title
//            stick: true // maintain when user navigates (see docs on the renderEvent method)
//        });
//        // make the event draggable using jQuery UI
//        $(this).draggable({
//            zIndex: 999,
//            revert: true,      // will cause the event to go back to its
//            revertDuration: 0  // original position after the drag
//        });
//    });
//
//EOF;
//$this->registerJs($DragJS);
//?>

<div class="event-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?=  Html::encode($this->title) ?></h3>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php


// выбираем мышкой область или кликаем в пустое поле
$JSSelect = <<<EOF
    function(start, end, jsEvent, view, resource ) {
        var eventData;
            eventData = {
                id: 0,
                start: start.format(),
                end: end.format(),
                resourceId: resource ? resource.id : null
            };
      
        console.log('выбираем мышкой область или кликаем в пустое поле');
        console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/init-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
               // console.log(res);
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
    function(calEvent, jsEvent, view) {

        var eventData;
            eventData = {
                id: calEvent.id,
            };
        console.log('кликаем по событию');
        console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/init-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
               // console.log(res);
            showDay(res);
            },
            error: function () {
                alert('Error!!!');
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
                resourceId: event.resourceId
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
                resourceId: event.resourceId
            };

     console.log('перетаскиваем событие, удерживая мышкой');
     console.log(eventData);
      $.ajax({
            url: '/admin/calendar/event/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
            $('#w0').fullCalendar('refetchEvents', JSON);
                //  console.log(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
      }
EOF;


// бросаем событие извне
$JSDrop = <<<EOF
    function(date, jsEvent, ui, resource) {
      if ($('#drop-remove').is(':checked')) {
             $(this).remove();
             }
        var eventData;
            eventData = {
                id: 0,
                title:  ui.helper[0].innerText,
                start: date.format(),
                end: date.format(),
                resourceId: null
            };
            console.log('бросаем событие извне', eventData);
      
      $.ajax({
            url: '/admin/calendar/event/init-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
            showDay(res);
         
                 // console.log(res);
            },
            error: function () {
                alert('Error!!!');

            }
        });
    }
EOF;
?>
             <div class="row">
                <div class="col-md-10">

                    <?= \backend\widgets\fullcalendarscheduler\src\FullcalendarScheduler::widget([
                                'options' => [
                                    'lang' => 'ru',
                                ],
                                'header' => [
                                    'left'   => 'today prev,next',
                                    'center' => 'title',
                                    'right'  => 'timelineDay,timelineThreeDays,agendaWeek,month,listMonth',
                                ],
                                'clientOptions' => [
                                    'schedulerLicenseKey' => 'GPL-My-Project-Is-Open-Source',
                                    'selectable' => true, // разрешено выбирать область
                                    'selectHelper' => true,//Следует ли рисовать событие” заполнитель " во время перетаскивания
                                    'nowIndicator' => true, //Отображение маркера, указывающего Текущее время
                                    'minTime' => '08:00:00', // Определяет первый временной интервал, который будет отображаться для каждого дня
                                    'maxTime' => '22:00:00', 
                                    'slotDuration' => '00:15:00', // Частота отображения временных интервалов.
                                    'droppable' => true,
                                    'editable' => true,
                                    //'eventDurationEditable' => true, // разрешить изменение размера
                                    'eventOverlap' => true, // разрешить перекрытие событий
                                    'eventLimit' => true,
                                    'drop' => new JsExpression($JSDrop),
                                    'select' => new JsExpression($JSSelect),
                                    'eventClick' => new JsExpression($JSEventClick),
                                    'eventResize'=> new JsExpression($JSEventResize),
                                    'eventDrop'=> new JsExpression($JSEventDrop),
                                    'defaultDate' => date('Y-m-d H:i'),
                                    'defaultTimedEventDuration' => '02:00:00', // при перетаскивании события в календарь задается длительность события
                                    'defaultAllDayEventDuration' => [
                                        'days' => '1'// то-же при перетаскиваниив в allDay
                                    ],
                                    'aspectRatio'       => 1.8,
                                    'scrollTime'        => '00:00', // undo default 6am scrollTime
                                    'defaultView' => 'agendaWeek',

                                    'views'             => [
                                        'timelineThreeDays' => [
                                            'type'     => 'timeline',
                                            'duration' => [
                                                'days' => 3,
                                            ],
                                        ],
                                    ],
                                    'resourceLabelText' => 'Auditories',
                                            'resourceGroupField' => 'building',
                                    'resources' => \yii\helpers\Url::to(['/calendar/event/resources']),
                                    'events' => \yii\helpers\Url::to(['/calendar/event/calendar']),
                                    'businessHours' => [
                                                 'dow' => [ 1, 2, 3, 4, 5, 6 ],
                                                 'start' => '10:00',
                                                 'end' => '20:00',
                                    ],
                                ],
]);
?>


</div>
                <div class="col-md-2">

                          <div id="external-events">
                                <h4>Draggable Events</h4>
                            <?php $data = EventCategory::getEventCategoryList();?>
                            <?php foreach ($data as $field) : ?>
                               <div class="fc-event ui-draggable ui-draggable-handle"
                                    style="background-color: <?= $field['color']; ?>;
                                            border-color: <?= $field['border_color']; ?>;
                                            color: <?= $field['text_color']; ?>;">
                                    <?= $field['name']; ?></div>
                            <?php  endforeach;?>
                                <p>
                                    <input type="checkbox" id="drop-remove">
                                    <label for="drop-remove">remove after drop</label>
                                </p>
                    </div>

                </div>
            </div>
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
//var event = $('<div />')
//      event.css({
//        'background-color': currColor,
//        'border-color'    : currColor,
//        'color'           : currColor,
//      }).addClass('external-event')
//      event.html(val)
//      $('#external-events').prepend(event)
//
function showDay(res) {

    $('#event-modal .modal-body').html(res);
    $('#event-modal').modal();
}
JS;

$this->registerJs($js);
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

