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
            url: '/admin/teachers/schedule/change-schedule',
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
            url: '/admin/teachers/schedule/update-schedule',
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
            Расписание занятий: <?php echo \artsoft\helpers\RefBook::find('teachers_fio')->getValue($model->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="row">

                <div class="col-sm-12">
                    <?= WeeklyScheduler::widget([
                        'readonly' => $readonly,
                        'data' => $model->getTeachersSchedule($model_date->plan_year),
                        'events' => [
//                            'onChange' => new JsExpression($JSChange),
//                            'onClick' => new JsExpression($JSEventClick),
                            // 'onScheduleClick' => new JsExpression($JSScheduleClick),
                        ]
                    ]);
                    ?>
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
]); ?>
<?php \yii\bootstrap\Modal::end(); ?>

