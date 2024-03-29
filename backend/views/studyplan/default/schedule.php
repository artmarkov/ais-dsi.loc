<?php

use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;
use yii\widgets\Pjax;

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
            url: '/admin/studyplan/schedule/change-schedule',
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
                studyplan_id: data.data.studyplan_id                 
            };
        
        console.log('кликаем по событию');
        console.log(eventData);
      $.ajax({
            url: '/admin/studyplan/schedule/update-schedule',
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
    'id' => 'subject-schedule-pjax',
])
?>
    <div class="subject-sect-schedule">
        <div class="panel">
            <div class="panel-heading">
                Расписание занятий: <?php echo \artsoft\helpers\RefBook::find('students_fullname')->getValue($model->student_id); ?>
                <?= $model->getProgrammName() . ' - ' . $model->course . ' класс.';?>
            </div>
            <div class="panel-body">
                <?= $this->render('_search-studyplan', compact('model_date')) ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-sm-12">
                                <?= WeeklyScheduler::widget([
                                    'readonly' => $readonly,
                                    'data' => $model->getStudyplanSchedule(),
                                    'events' => [
//                                        'onChange' => new JsExpression($JSChange),
//                                        'onClick' => new JsExpression($JSEventClick),
                                       // 'onScheduleClick' => new JsExpression($JSScheduleClick),
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
   // 'header' => '<h3 class="lte-hide-title page-title">Расписание</h3>',
    'size' => 'modal-lg',
    'id' => 'schedule-modal',
]);?>
<?php \yii\bootstrap\Modal::end(); ?>

