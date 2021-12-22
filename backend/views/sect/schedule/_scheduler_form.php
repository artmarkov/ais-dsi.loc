<?php

use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;

$JSChange = <<<EOF
        function(node, data) {
         eventData = {   
               id: data.data.schedule_id, 
               week_day: data.timeline,
               time_in: data.start,
               time_out: data.end,  
            };
        console.log(data);
             $.ajax({
            url: '/admin/sect/schedule/change-schedule',
            type: 'POST',
            data: {eventData: eventData},
            success: function (res) {
                console.log(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
        }
EOF;
// кликаем по событию
$JSEventClick = <<<EOF
    function(node, data) {
        eventData = {   
                studyplan_id: data.data.studyplan_id,
                id: data.data.schedule_id,               
            };
    // change the border color just for fun
   // node.addClass('sc_bar_photo');
   // node.addStyle('red');
        
        console.log('кликаем по событию');
        console.log(data);
      $.ajax({
            url: '/admin/sect/schedule/init-schedule',
            type: 'POST',
            data: {eventData: eventData},
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
$JSScheduleClick = <<<EOF
        function(node, time, timeline){
                var studyplanId = ''; 
                var start = time;
                var end = $(this).timeSchedule('formatTime', $(this).timeSchedule('calcStringTime', time) + 2700);
                $(this).timeSchedule('addSchedule', timeline, {
                    start: start,
                    end: end,
                    text:'Новая запись',
                    data:{
                        class: 'sc_bar_insert'
                    }
                });
//                node.css({
//          background: #ccc;
//         
//        });
                var eventData = {
                id: 0,
                week_day: timeline,         
                time_in: start,          
                time_out: end,          
                studyplan_id: studyplanId         
            };
               console.log('кликаем по календ');
                console.log(node);
                console.log(eventData);
                $.ajax({
            url: '/admin/sect/schedule/init-schedule',
            type: 'POST',
            data: {eventData: eventData},
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
?>
<div class="subject-sect-schedule">
    <div class="panel panel-info">
        <div class="panel-heading">
            Расписание занятий
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= WeeklyScheduler::widget([
                        'data' => $model->getSubjectSectSchedule(),
                        'events' => [
                                    'onChange' => new JsExpression($JSChange),
                                    'onClick' => new JsExpression($JSEventClick),
                                    'onScheduleClick' => new JsExpression($JSScheduleClick),
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \yii\bootstrap\Modal::begin([
    'header' => '<h3 class="lte-hide-title page-title">Расписание</h3>',
    'size' => 'modal-md',
    'id' => 'schedule-modal',
]);

\yii\bootstrap\Modal::end(); ?>