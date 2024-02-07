<?php


use artsoft\helpers\Html;
use common\models\auditory\Auditory;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//echo '<pre>' . print_r($events, true) . '</pre>';

$this->title = 'Ежедневник по преподавателям';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-index">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
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
                allDay: e.allDay,
                resourceId: e.resource.id
            };
        console.log('выбираем мышкой область или кликаем в пустое поле');
        console.log(e.resource.id);
      $.ajax({
            url: '/admin/activities/schedule/create-event',
            type: 'POST',
            data: {eventData : eventData},
           success: function (res) {
//                console.log(res);
                $('#schedule-modal .modal-body').html(res);
                $('#schedule-modal').modal();
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
                resource: e.event.extendedProps.source,        
            };
    // change the border color just for fun
    e.el.style.borderColor = 'red';
        
        console.log('кликаем по событию ' + e.event.id);
       $.ajax({
            url: '/admin/activities/default/create-event',
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

            $JSEventClickFront = <<<EOF
    function(e) {
        eventData = {
                id: e.event.id,              
                resource: e.event.extendedProps.source,           
            };
    // change the border color just for fun
    e.el.style.borderColor = 'red';
        
        console.log('кликаем по событию ' + e.event.id);
         console.log(e.event);
      $.ajax({
            url: '/activities/default/create-event',
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
             url: '/admin/activities/schedule/refactor-event',
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
                allDay: e.event.allDay,
                resourceId: e.newResource === null ? null : e.newResource.id
            };
         console.log('перетаскиваем событие, удерживая мышкой');
         console.log(e.newResource);
         $.ajax({
             url: '/admin/activities/schedule/refactor-event',
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
            ?>
            <?= \common\widgets\fullcalendarscheduler\src\FullcalendarScheduler::widget([
                   'headerToolbar' => [
                        'left' => 'today prev,next',
                        'center' => 'title',
                        'right' => 'resourceTimelineDay,resourceTimelineThreeDays',
                    ],
                'clientOptions' => [
                    'schedulerLicenseKey' => 'GPL-My-Project-Is-Open-Source',
                    'initialView' => 'resourceTimelineDay',
                    'aspectRatio' => 1.8,
                    'height' => 'auto', // 'auto' - aspectRatio no works
                    'navLinks' => true,
                    'editable' => false,
                    'selectable' => true,// разрешено выбирать область
                    'expandRows' => true,
                    'nowIndicator' => true, //Отображение маркера, указывающего Текущее время
                    'slotMinTime' => '09:00',// Определяет первый временной интервал, который будет отображаться для каждого дня
                    'slotMaxTime' => '22:00',
                    'slotDuration' => '00:10:00', // Частота отображения временных интервалов.
                    'eventDurationEditable' => true, // разрешить изменение размера
                    'eventOverlap' => true, // разрешить перекрытие событий
                    'views' => [
                        'resourceTimelineThreeDays' => [
                            'type' => 'resourceTimeline',
                            'duration' => ['days' => 3],
                            'buttonText' => Yii::t('art/calendar', '3 days'),
                        ]
                    ],
//                    'select' => new JsExpression($JSSelect),
                    'eventClick' => new JsExpression(\artsoft\Art::isBackend() ? $JSEventClick : $JSEventClickFront),
//                    'eventResize' => new JsExpression($JSEventResize),
//                    'eventDrop' => new JsExpression($JSEventDrop),
                    'resourceAreaHeaderContent' => Yii::t('art/calendar', 'Teachers'),
                    'resourceGroupField' => 'parent',
                    'resources' => \yii\helpers\Url::to(['/activities/teachers-schedule/teachers']),
                    'events' => \yii\helpers\Url::to(['/activities/teachers-schedule/init-calendar']),
                ],
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

