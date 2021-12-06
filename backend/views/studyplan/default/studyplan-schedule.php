<?php

use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;

/* @var $readonly */
/* @var $modelsSubject */

$JSInit = <<<EOF
    function(event, val, form) {
    console.log(event);
    console.log(val);
    console.log(form);
    location.reload();
    }
EOF;
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
                        Нагрузка преподавателей и расписание занятий
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">№</th>
                                        <th class="text-center">Дисциплина</th>
                                        <th class="text-center">Час/нед</th>
                                        <th class="text-center">Группа</th>
                                        <th class="text-center">Преподаватель(нагр)</th>
                                        <th class="text-center">Расписание занятий</th>
                                        <th class="text-center">Аудитория</th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                        <tr class="item">
                                            <td>
                                                <?= ++$index; ?>
                                            </td>
                                            <td>
                                                <?= RefBook::find('subject_memo_2')->getValue($modelSubject->id ?? null) ?>
                                            </td>
                                            <td>
                                                <?= $modelSubject->week_time; ?>
                                            </td>
                                            <td>
                                                <?php if (!$modelSubject->isIndividual()): ?>
                                                    <?= Editable::widget([
                                                        'name' => 'id_' . $modelSubject->id,
                                                        'value' => $modelSubject->getSubjectSectStudyplan()->id,
                                                        'attribute' => 'id',
                                                        'header' => 'группу',
                                                        'displayValueConfig' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                        'asPopover' => true,
                                                        'format' => Editable::FORMAT_LINK,
                                                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                        'data' => $modelSubject->getSubjectSectStudyplanAll() ?? [],
                                                        'options' => ['class' => 'form-control'],
                                                        'formOptions' => [
                                                            'action' => Url::toRoute([
                                                                '/studygroups/default/set-group',
                                                                'studyplan_subject_id' => $modelSubject->id ?? null
                                                            ]),
                                                        ],
                                                    ]);
                                                    ?>
                                                <?php else: ?>
                                                    <?= $modelSubject->getSubjectVidName(); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                            <?php foreach ($modelSubject->getTeachersLoads() as $item => $modelTeachersLoad): ?>
                                            <?= Editable::widget([
                                                'name' => "teachers[$modelSubject->id][$modelTeachersLoad->id]",
                                                'value' => $modelTeachersLoad->teachers_id,
                                                'attribute' => 'teachers_id',
                                                'header' => 'нагрузку',
                                                'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                'asPopover' => true,
                                                'format' => Editable::FORMAT_LINK,
                                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                'data' => $modelSubject->getSubjectTeachers(),
                                                'options' => ['class' => 'form-control'],
                                                'formOptions' => [
                                                    'action' => Url::toRoute([
                                                        '/teachers/default/set-load',
                                                        'teachers_load_id' => $modelTeachersLoad->id,
                                                        'studyplan_subject_id' => $modelSubject->id
                                                    ]),
                                                ],
                                                    'pluginEvents' => [
//                                                        "editableChange"=>"function(event, val) { log('Changed Value ' + val); }",
                                                        "editableSubmit"=> new JsExpression($JSInit),
//                                                        "editableBeforeSubmit"=>"function(event, jqXHR) { log('Before submit triggered'); }",
//                                                        "editableSubmit"=>"function(event, val, form, jqXHR) { log('Submitted Value ' + val); }",
//                                                        "editableReset"=>"function(event) { log('Reset editable form'); }",
//                                                        "editableSuccess"=>"function(event, val, form, data) { log('Successful submission of value ' + val); }",
//                                                        "editableError"=>"function(event, val, form, data) { log('Error while submission of value ' + val); }",
//                                                        "editableAjaxError"=>"function(event, jqXHR, status, message) { log(message); }",
                                                    ]
                                            ]);
                                            ?>
                                            <?php endforeach;?>
                                            <?= Editable::widget([
                                                'name' => "teachers[$modelSubject->id][0]",
                                                'value' => '',
                                                'attribute' => 'teachers_id',
                                                'header' => 'нагрузку',
                                                'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                'asPopover' => true,
                                                'valueIfNull' => false,
                                                'defaultEditableBtnIcon' => '<i class="glyphicon glyphicon-plus"></i>',
                                                'format' => Editable::FORMAT_BUTTON,
                                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                'data' => $modelSubject->getSubjectTeachers(),
                                                'options' => ['class' => 'form-control'],
                                                'formOptions' => [
                                                    'action' => Url::toRoute([
                                                        '/teachers/default/set-load',
                                                        'studyplan_subject_id' => $modelSubject->id
                                                    ]),
                                                ],
                                                'pluginEvents' => [
//                                                        "editableChange"=>"function(event, val) { log('Changed Value ' + val); }",
                                                    "editableSubmit"=> new JsExpression($JSInit),
//                                                        "editableBeforeSubmit"=>"function(event, jqXHR) { log('Before submit triggered'); }",
//                                                        "editableSubmit"=>"function(event, val, form, jqXHR) { log('Submitted Value ' + val); }",
//                                                        "editableReset"=>"function(event) { log('Reset editable form'); }",
//                                                        "editableSuccess"=>"function(event, val, form, data) { log('Successful submission of value ' + val); }",
//                                                        "editableError"=>"function(event, val, form, data) { log('Error while submission of value ' + val); }",
//                                                        "editableAjaxError"=>"function(event, jqXHR, status, message) { log(message); }",
                                                ]
                                            ]);
                                            ?>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
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




