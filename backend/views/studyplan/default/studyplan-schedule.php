<?php

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\jqueryscheduler\WeeklyScheduler;
use kartik\depdrop\DepDrop;
use kartik\popover\PopoverX;
use yii\widgets\MaskedInput;
use common\models\teachers\Teachers;

/* @var $readonly */
/* @var $modelsSubject */

$JSSubmit = <<<EOF
    function(event, val, form) {
//    console.log(event);
//    console.log(val);
//    console.log(form);
    $.pjax.reload({container: '#studyplan-grid-pjax', async: true});
    }
EOF;

$JSErr = <<<EOF
    function(event, val, form, data) {
   
    console.log(event);
    console.log(val);
    console.log(form);
    console.log(data);
    }
EOF;
?>
<?php
\yii\widgets\Pjax::begin([
    'id' => 'studyplan-grid-pjax',
])
?>
    <div class="panel">
        <div class="panel-heading">
            Расписание занятий
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Расписание занятий
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Предмет(Час/нед)</th>
                                            <th class="text-center">Группа</th>
                                            <th class="text-center">Нагрузка</th>
                                            <th class="text-center">Расписание занятий</th>
                                        </tr>
                                        </thead>
                                        <tbody class="container-items">
                                        <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                            <tr class="item">
                                                <td>
                                                    <?= RefBook::find('subject_memo_2')->getValue($modelSubject->id ?? null) . '-' . $modelSubject->week_time; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$modelSubject->isIndividual()): ?>
                                                        <?= Editable::widget([
                                                            'id' => $modelSubject->id,
                                                            'name' => "sect_id[{$modelSubject->id}]",
                                                            'value' => $modelSubject->getSubjectSectStudyplan()->id,
                                                            'header' => 'Изменить группу',
                                                            'displayValueConfig' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                            'format' => Editable::FORMAT_LINK,
                                                            'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                            'data' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                            'options' => ['class' => 'form-control'],
                                                            'formOptions' => [
                                                                'action' => Url::toRoute([
                                                                    '/subjectsect/default/set-group',
                                                                    'studyplan_subject_id' => $modelSubject->id ?? null
                                                                ]),
                                                            ],
                                                            'pluginEvents' => [
                                                                "editableSubmit" => new JsExpression($JSSubmit),
                                                            ],
                                                            'dataAttributes' => ['sect_id' => $modelSubject->getSubjectSectStudyplan()->id]
                                                        ]);
                                                        ?>
                                                    <?php else: ?>
                                                        <?= $modelSubject->getSubjectVidName(); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?php foreach ($modelSubject->getTeachersLoads() as $item => $modelTeachersLoad): ?>
                                                        <div>
                                                            <?php
                                                            $editable = Editable::begin([
                                                                'model' => $modelTeachersLoad,
                                                                'attribute' => "[{$modelSubject->id}][{$modelTeachersLoad->id}]teachers_id",
                                                                'displayValue' => RefBook::find('teachers_load_display')->getValue($modelTeachersLoad->id),
                                                                'header' => 'Изменить нагрузку',
                                                                'format' => Editable::FORMAT_LINK,
                                                                'inputType' => Editable::INPUT_DEPDROP,
                                                                'options' => [
                                                                    'id' => $modelSubject->id . "-" . $modelTeachersLoad->id,
                                                                    'type' => DepDrop::TYPE_SELECT2,
                                                                    'options' => ['placeholder' => Yii::t('art/teachers', 'Select Teacher...')],
                                                                    'select2Options' => [
                                                                        'pluginOptions' => [
                                                                            'dropdownParent' => "#" . $modelSubject->id . "-" . $modelTeachersLoad->id . "-popover",
                                                                            'allowClear' => true,
                                                                        ]
                                                                    ],
                                                                    'data' => Teachers::getTeachersList($modelTeachersLoad->direction_id),
                                                                    'pluginOptions' => [
                                                                        'depends' => [$modelSubject->id . "-" . $modelTeachersLoad->id . "-direction_id"],
                                                                        'url' => Url::to(['/teachers/default/teachers'])
                                                                    ]
                                                                ],
                                                                'formOptions' => [
                                                                    'action' => Url::toRoute([
                                                                        '/teachers/default/set-load',
                                                                        'teachers_load_id' => $modelTeachersLoad->id,
                                                                        'studyplan_subject_id' => $modelSubject->id
                                                                    ]),
                                                                ],
                                                                'pluginEvents' => [
                                                                    "editableSubmit" => new JsExpression($JSSubmit),
                                                                ]
                                                            ]);
                                                            $form = $editable->getForm();
                                                            $editable->beforeInput = \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}][{$modelTeachersLoad->id}]kv-editable-depdrop", '1')
                                                                .
                                                                $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]direction_id")
                                                                    ->dropDownList(['' => Yii::t('art/teachers', 'Select Direction...')] + \common\models\guidejob\Direction::getDirectionList(),
                                                                        ['id' => $modelSubject->id . "-" . $modelTeachersLoad->id . "-direction_id"])->label(false) . "\n";

                                                            $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]week_time")->textInput(['placeholder' => Yii::t('art/guide', 'Enter week time...')])->label(false) . "\n";
                                                            Editable::end();
                                                            ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="pull-right">
                                                        <?php
                                                        $modelTeachersLoad = new \common\models\teachers\TeachersLoad();
                                                        $editable = Editable::begin([
                                                            'model' => $modelTeachersLoad,
                                                            'attribute' => "[{$modelSubject->id}][0]teachers_id",
                                                            'header' => 'Добавить нагрузку',
                                                            'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                            'valueIfNull' => 'новая запись',
                                                            'buttonsTemplate' => "{reset}{submit}",
                                                            'format' => Editable::FORMAT_LINK,
                                                            'inputType' => Editable::INPUT_DEPDROP,
                                                            'options' => [
                                                                'id' => $modelSubject->id . "-teachers-load",
                                                                'type' => DepDrop::TYPE_SELECT2,
                                                                'options' => ['placeholder' => Yii::t('art/teachers', 'Select Teacher...')],
                                                                'select2Options' => [
                                                                    'pluginOptions' => [
                                                                        'dropdownParent' => "#" . $modelSubject->id . "-teachers-load-popover",
                                                                        'allowClear' => true,
                                                                    ]
                                                                ],
                                                                'data' => Teachers::getTeachersList($modelTeachersLoad->direction_id),
                                                                'pluginOptions' => [
                                                                    'depends' => [$modelSubject->id . "-direction_id"],
                                                                    'url' => Url::to(['/teachers/default/teachers'])
                                                                ]
                                                            ],
                                                            'formOptions' => [
                                                                'action' => Url::toRoute([
                                                                    '/teachers/default/set-load',
                                                                    'studyplan_subject_id' => $modelSubject->id
                                                                ]),
                                                            ],
                                                            'pluginEvents' => [
                                                                "editableSubmit" => new JsExpression($JSSubmit),
                                                            ],

                                                        ]);
                                                        $form = $editable->getForm();
                                                        $editable->beforeInput = \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}]kv-editable-depdrop", '1')
                                                            .
                                                            $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]direction_id")
                                                                ->dropDownList(['' => Yii::t('art/teachers', 'Select Direction...')] + \common\models\guidejob\Direction::getDirectionList(),
                                                                    ['id' => $modelSubject->id . "-direction_id"])->label(false) . "\n";

                                                        $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]week_time")->textInput(['placeholder' => Yii::t('art/guide', 'Enter week time...')])->label(false) . "\n";
                                                        Editable::end();
                                                        ?>
                                                    </div>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?php foreach ($modelSubject->getSubjectSectSchedule() as $item => $modelSectSchedule): ?>
                                                        <div>
                                                            <?php
                                                            $editable = Editable::begin([
                                                                'model' => $modelSectSchedule,
                                                                'attribute' => "[{$modelSubject->id}][{$modelSectSchedule->id}]teachers_load_id",
                                                                'displayValue' => $modelSectSchedule->getTeachersScheduleDisplay(),
                                                                'header' => 'Изменить расписание',
                                                                'format' => Editable::FORMAT_LINK,
                                                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                                'data' => $modelSubject->getTeachersLoadsDisplay(),
                                                                'formOptions' => [
                                                                    'action' => Url::toRoute([
                                                                        '/subjectsect/schedule/set-schedule',
                                                                        'schedule_id' => $modelSectSchedule->id,
                                                                        'studyplan_subject_id' => $modelSubject->id
                                                                    ]),
                                                                ],
                                                                'pluginEvents' => [
                                                                    "editableSuccess" => new JsExpression($JSSubmit),
                                                                    "editableAjaxError" => new JsExpression($JSErr),

                                                                ],

                                                            ]);
                                                            $form = $editable->getForm();
                                                            $editable->afterInput = $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList())->label(false) .
                                                                $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList())->label(false) .
                                                                $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]time_in")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time in...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')])->label(false) .
                                                                $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]time_out")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time out...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')])->label(false) .
                                                                $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList())->label(false) .
                                                                $form->field($modelSectSchedule, "[{$modelSubject->id}][{$modelSectSchedule->id}]description")->textarea(['placeholder' => Yii::t('art/guide', 'Enter description...')])->label(false) . "\n";
                                                            Editable::end();
                                                            ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="pull-right">
                                                        <?php
                                                        //print_r($modelSubject->getTeachersLoadsDisplay());
                                                        $modelSectSchedule = new \common\models\subjectsect\SubjectSectSchedule();
                                                        $editable = Editable::begin([
                                                            'model' => $modelSectSchedule,
                                                            'attribute' => "[{$modelSubject->id}][0]teachers_load_id",
                                                            // 'displayValue' => $modelSectSchedule->getTeachersScheduleDisplay(),
                                                            'header' => 'Добавить расписание',
                                                            'valueIfNull' => 'новая запись',
                                                            'buttonsTemplate' => "{reset}{submit}",
                                                            'format' => Editable::FORMAT_LINK,
                                                            'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                            'data' => $modelSubject->getTeachersLoadsDisplay(),
                                                            'formOptions' => [
                                                                'action' => Url::toRoute([
                                                                    '/subjectsect/schedule/set-schedule',
                                                                    'studyplan_subject_id' => $modelSubject->id
                                                                ]),
                                                            ],
                                                            'pluginEvents' => [
                                                                "editableSubmit" => new JsExpression($JSSubmit),
                                                            ],

                                                        ]);
                                                        $form = $editable->getForm();
                                                        $editable->afterInput = $form->field($modelSectSchedule, "[{$modelSubject->id}][0]week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList())->label(false) .
                                                            $form->field($modelSectSchedule, "[{$modelSubject->id}][0]week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList())->label(false) .
                                                            $form->field($modelSectSchedule, "[{$modelSubject->id}][0]time_in")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time in...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')])->label(false) .
                                                            $form->field($modelSectSchedule, "[{$modelSubject->id}][0]time_out")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time out...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')])->label(false) .
                                                            $form->field($modelSectSchedule, "[{$modelSubject->id}][0]auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList())->label(false) .
                                                            $form->field($modelSectSchedule, "[{$modelSubject->id}][0]description")->textarea(['placeholder' => Yii::t('art/guide', 'Enter description...')])->label(false) . "\n";
                                                        Editable::end();
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php \yii\widgets\Pjax::end() ?>
<?php
$js = <<<JS
$('.kv-editable-remove').on('click', function (e) {
         e.preventDefault();
         var a = $(this).parent().find("div").val();
         console.log(e);
    console.log(this);
        console.log(a);
});
JS;

$this->registerJs($js);
?>
<?php
$JSClick = <<<EOF
        function(node, data) {
        console.log(node);
        console.log(data);
        }
EOF;
$JSChange = <<<EOF
        function(node, data) {
        console.log(node);
        console.log(data);
        }
EOF;

$JSScheduleClick = <<<EOF
        function(node, time, timeline){
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
                console.log(node);
                console.log(time);
                console.log(timeline);
            }
        
EOF;
?>

    <div class="panel panel-info">
        <div class="panel-heading">
            График
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= WeeklyScheduler::widget([
                        'data' => [
                            [
                                'week_day' => 1,
                                'time_in' => '10:00',
                                'time_out' => '12:13',
                                'title' => 'Название',
                                'data' => [
                                    'week_num' => 1,
                                    'class' => 'sc_bar_insert'
                                ]
                            ]
                        ],
                        'events' => [
                            'onChange' => new JsExpression($JSChange),
                            'onClick' => new JsExpression($JSClick),
                            'onScheduleClick' => new JsExpression($JSScheduleClick),
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
$css = <<<CSS
.sc_bar_insert{
            background-color: #ff678a;
        }
CSS;

$this->registerCss($css);
?>