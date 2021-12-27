<?php

use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;
use yii\widgets\Pjax;

$this->title = Yii::t('art/guide', 'Subject Sect Schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$JSChange = <<<EOF
        function(node, data) {
         eventData = {   
               id: data.data.schedule_id, 
               week_day: data.timeline,
               time_in: data.start,
               time_out: data.end,  
            };
            console.log('измен. событию');
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
                id: data.data.schedule_id, 
                subject_sect_id: data.data.subject_sect_id                 
            };
        
        console.log('кликаем по событию');
//        console.log(data);
      $.ajax({
            url: '/admin/sect/schedule/update-schedule',
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
                var subjectSectId = '$model->id'; 
                var start = time;
                var end = $(this).timeSchedule('formatTime', $(this).timeSchedule('calcStringTime', time) + 2700);
                $(this).timeSchedule('addSchedule', timeline, {
                    start: start,
                    end: end,
                    text:'Новая запись'
                });
                var eventData = {
                id: 0,
                week_day: timeline,         
                time_in: start,          
                time_out: end,          
                subject_sect_id: subjectSectId         
            };
               console.log('кликаем по календ');
//                console.log(node);
//                console.log(eventData);
                $.ajax({
            url: '/admin/sect/schedule/update-schedule',
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
<?php
Pjax::begin([
    'id' => 'subject-sect-schedule-pjax',
])
?>
    <div class="subject-sect-schedule">
        <div class="panel">
            <div class="panel-heading">
                <?= $this->title; ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-sm-12">
                                <?= WeeklyScheduler::widget([
                                    'readonly' => $readonly,
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
        </div>
    </div>
<?php Pjax::end() ?>

<?php \yii\bootstrap\Modal::begin([
    'header' => '<h3 class="lte-hide-title page-title">Расписание</h3>',
    'size' => 'modal-md',
    'id' => 'schedule-modal',
]);?>
<?php \yii\bootstrap\Modal::end(); ?>

