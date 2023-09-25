<?php

use artsoft\helpers\Html;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//echo '<pre>' . print_r($events, true) . '</pre>';

$this->title = Yii::t('art/calendar', 'Activities calendar');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-index">
    <div class="panel">
        <div class="panel-body">
            <?= \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info"></i> Кликните на мероприятие для детализации.',
                'options' => ['class' => 'alert-info'],
            ]);
            ?>
            <div class="col-sm-12"

            <?php
            // выбираем мышкой область или кликаем в пустое поле
            $JSSelect = <<<EOF
    function(e) {
    var start = e.start.getFullYear()+ '/' + ('0'+ (e.start.getMonth()+1)).slice(-2)+'/'+ ('0' + e.start.getDate()).slice(-2)+' '+ ('0' + e.start.getHours()).slice(-2)+':'+ ('0' + e.start.getMinutes()).slice(-2);
    var end = e.end.getFullYear()+ '/' + ('0'+ (e.end.getMonth()+1)).slice(-2)+'/'+ ('0' + e.end.getDate()).slice(-2)+' '+ ('0' + e.end.getHours()).slice(-2)+':'+ ('0' + e.end.getMinutes()).slice(-2);
        var eventData;
            eventData = {
                id: 0,
                start: start,
                end: end,
                allDay: e.allDay
            };
        console.log('выбираем мышкой область или кликаем в пустое поле');
        console.log(eventData);
      $.ajax({
            url: '/admin/activities/default/create-event',
            type: 'POST',
            data: {eventData : eventData},
           success: function (res) {
//                console.log(res);
                $('#activities-modal .modal-body').html(res);
                $('#activities-modal').modal();
            },
            error: function () {
                alert('Error!!!');
            }
        });
    }
EOF;
            // кликаем по событию
            $JSEventClick = <<<EOF
    function(e) {
        eventData = {
                id: e.event.id,              
            };
    // change the border color just for fun
    e.el.style.borderColor = 'red';
        
        console.log('кликаем по событию ' + e.event.id);
        console.log(e.event);
      $.ajax({
            url: '/admin/activities/schoolplan-outside/create-event',
            type: 'POST',
            data: {eventData: eventData},
            success: function (res) {
//                console.log(res);
                $('#activities-modal .modal-body').html(res);
                $('#activities-modal').modal();
            },
            error: function () {
                alert('Error!!!');
            }
        });
    }

EOF;

            // кликаем по событию
            $JSEventClickFront = <<<EOF
    function(e) {
        eventData = {
                id: e.event.id,              
            };
    // change the border color just for fun
    e.el.style.borderColor = 'red';
        
        console.log('кликаем по событию ' + e.event.id);
        console.log(e.event);
      $.ajax({
            url: '/activities/schoolplan-outside/create-event',
            type: 'POST',
            data: {eventData: eventData},
            success: function (res) {
//                console.log(res);
                $('#activities-modal .modal-body').html(res);
                $('#activities-modal').modal();
            },
            error: function () {
                alert('Error!!!');
            }
        });
    }

EOF;
            // растягиваем/сжимаем событие мышкой
            $JSEventResize = <<<EOF
    function(e) {
        var start = e.event.start.getFullYear()+ '/' + ('0'+ (e.event.start.getMonth()+1)).slice(-2)+'/'+ ('0' + e.event.start.getDate()).slice(-2)+' '+ ('0' + e.event.start.getHours()).slice(-2)+':'+ ('0' + e.event.start.getMinutes()).slice(-2);
        var end = e.event.start.getFullYear()+ '/' + ('0'+ (e.event.end.getMonth()+1)).slice(-2)+'/'+ ('0' + e.event.end.getDate()).slice(-2)+' '+ ('0' + e.event.end.getHours()).slice(-2)+':'+ ('0' + e.event.end.getMinutes()).slice(-2);
        var eventData;
            eventData = {
                id: e.event.id, 
                start: start,
                end: end,
                allDay: e.event.allDay
            };
         console.log('растягиваем/сжимаем событие мышкой');
        console.log(eventData);
         $.ajax({
             url: '/admin/activities/default/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
                  console.log('success');
            },
            error: function () {
               console.log('error');
            }
        });
      }
EOF;
            // перетаскиваем событие, удерживая мышкой
            $JSEventDrop = <<<EOF
    function(e) {
        var c = e.event;
        var start = c.start.getFullYear()+ '/' + ('0'+ (c.start.getMonth()+1)).slice(-2)+'/'+ ('0' + c.start.getDate()).slice(-2)+' '+ ('0' + c.start.getHours()).slice(-2)+':'+ ('0' + c.start.getMinutes()).slice(-2);
    if(e.event.allDay == true && e.event.end == null) {
        var end = c.start.getFullYear()+ '/' + ('0'+ (c.start.getMonth()+1)).slice(-2) + '/' + ('0' + (c.start.getDate()+1)).slice(-2) + ' ' + ('0' + c.start.getHours()).slice(-2)+':'+ ('0' + c.start.getMinutes()).slice(-2);
    }else if(e.event.allDay == false && e.event.end == null) {
        var end = c.start.getFullYear()+ '/' + ('0'+ (c.start.getMonth()+1)).slice(-2)+'/'+ ('0' + c.start.getDate()).slice(-2)+ ' ' + ('0' + (c.start.getHours()+1)).slice(-2)+':'+ ('0' + c.start.getMinutes()).slice(-2);
    }else {
        var end = c.end.getFullYear()+ '/' + ('0'+ (c.end.getMonth()+1)).slice(-2)+'/'+ ('0' + c.end.getDate()).slice(-2)+' '+ ('0' + c.end.getHours()).slice(-2)+':'+ ('0' + c.end.getMinutes()).slice(-2);
    }
        var eventData;
            eventData = {
                id: e.event.id, 
                start: start,
                end: end,
                allDay: e.event.allDay
            };
         console.log('перетаскиваем событие, удерживая мышкой');
         $.ajax({
             url: '/admin/activities/default/refactor-event',
            type: 'POST',
            data: {eventData : eventData},
            success: function (res) {
                  console.log('success');
            },
            error: function () {
               console.log('error');
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

            <?= \common\widgets\fullcalendar\src\Fullcalendar::widget([
                'clientOptions' => [
                    'initialView' => 'dayGridMonth',
                    'height' => 'auto', // 'auto' - aspectRatio no works
                    'aspectRatio' => 1.8,
                    'navLinks' => true,
                    'businessHours' => true,
                    'editable' => false,
                    'selectable' => false,
                    'expandRows' => false,
                    'nowIndicator' => true, //Отображение маркера, указывающего Текущее время
                    'slotMinTime' => '07:00',
                    'slotMaxTime' => '22:00',
                    'slotDuration' => '00:15:00', // Частота отображения временных интервалов.
                    'eventDurationEditable' => false, // разрешить изменение размера
                    'eventOverlap' => true, // разрешить перекрытие событий
                    'eventClick' => new JsExpression(\artsoft\Art::isBackend() ? $JSEventClick : $JSEventClickFront),
//                    'eventMouseEnter' => new JsExpression($JSOnDay),
//                    'eventMouseLeave' => new JsExpression($JSOutDay),
//                    'eventDrop' => new JsExpression($JSEventDrop),
//                    'select' => new JsExpression($JSSelect),
//                    'eventResize' => new JsExpression($JSEventResize),
                ],
                'events' => \yii\helpers\Url::to(['/activities/schoolplan-outside/init-calendar']),
            ]);
            ?>
        </div>
    </div>
</div>

<?php \yii\bootstrap\Modal::begin([
    'header' => '<h4 class="lte-hide-title page-title">Карточка мероприятия</h4>',
    'size' => 'modal-lg',
    'id' => 'activities-modal',
]);

\yii\bootstrap\Modal::end(); ?>


