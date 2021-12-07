<?php

use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;

/* @var $readonly */
/* @var $modelsSubject */

$JSSubmit = <<<EOF
    function(event, val, form) {
    console.log(event);
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
                                                        'name' => "sect_id[$modelSubject->id]",
                                                        'value' => $modelSubject->getSubjectSectStudyplan()->id,
                                                        'attribute' => 'sect_id',
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
                                                        'pluginEvents' => [
                                                            "editableSubmit" => new JsExpression($JSSubmit),
                                                        ]
                                                    ]);
                                                    ?>
                                                <?php else: ?>
                                                    <?= $modelSubject->getSubjectVidName(); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php foreach ($modelSubject->getTeachersLoads() as $item => $modelTeachersLoad): ?>
                                                    <?php
                                                    $editable = Editable::begin([
                                                        'model' => $modelTeachersLoad,
//                                                        'name' => "teachers[$modelSubject->id][$modelTeachersLoad->id]",
//                                                        'value' => $modelTeachersLoad->teachers_id,
                                                        'attribute' => "[{$modelSubject->id}][{$modelTeachersLoad->id}]teachers_id",
                                                        'header' => 'нагрузку',
                                                        'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                        'asPopover' => true,
                                                        'format' => Editable::FORMAT_LINK,
                                                        'inputType' => Editable::INPUT_DEPDROP,
                                                        'options' => [
                                                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                                            'options' => [ 'placeholder' => 'Select subcat...'],
                                                            'select2Options' => [
//                                                                'pluginOptions' => [
//                                                                   // 'dropdownParent' => '#subcat-id-p-popover', // set this to "#<EDITABLE_ID>-popover" to ensure select2 renders properly within the popover
//                                                                    'allowClear' => true,
//                                                                ]
                                                            ],
                                                            'pluginOptions'=>[
                                                                'depends'=>[$modelSubject->id."-".$modelTeachersLoad->id."-direction_id"],
                                                                'url' => Url::to(['/teachers/default/teachers'])
                                                            ]
                                                        ]
                                                    ]);
                                                    $form = $editable->getForm();
                                                    $editable->beforeInput =  \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}][{$modelTeachersLoad->id}]kv-editable-depdrop", '1')
                                                         .
                                                        $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]week_time")->textInput(['placeholder'=>'Enter week time...'])->label(false) .

                                                    $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]direction_id")
                                                        ->dropDownList(['' => 'Select cat...'] + \common\models\guidejob\Direction::getDirectionList(),
                                                        ['id'=> $modelSubject->id."-".$modelTeachersLoad->id."-direction_id"])->label(false);
                                                    Editable::end();
                                                    ?>
                                                <?php endforeach; ?>
                                                <?= Editable::widget([
                                                    'model' => new \common\models\teachers\TeachersLoad(),
//                                                    'name' => "teachers[$modelSubject->id][0]",
//                                                    'value' => '',
                                                    'attribute' => "[{$modelSubject->id}][0]teachers_id",
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
                                                        "editableSubmit" => new JsExpression($JSSubmit),
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




