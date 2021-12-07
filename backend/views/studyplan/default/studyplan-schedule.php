<?php

use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;
use kartik\depdrop\DepDrop;
use kartik\popover\PopoverX;
use artsoft\helpers\Html;
use common\models\teachers\Teachers;

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
                                                        'attribute' => "[{$modelSubject->id}][{$modelTeachersLoad->id}]teachers_id",
                                                        'header' => 'нагрузку',
                                                        'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                        'asPopover' => true,
                                                        'size' => PopoverX::SIZE_MEDIUM,
                                                        'format' => Editable::FORMAT_LINK,
                                                        'inputType' => Editable::INPUT_DEPDROP,
                                                        'options' => [
                                                            'id' => $modelSubject->id."-".$modelTeachersLoad->id,
                                                            'type' => DepDrop::TYPE_SELECT2,
                                                            'options' => ['placeholder' => 'Select teachers...'],
                                                            'select2Options' => [
                                                                'pluginOptions' => [
                                                                    'dropdownParent' => "#".$modelSubject->id."-".$modelTeachersLoad->id."-popover", // set this to "#<EDITABLE_ID>-popover" to ensure select2 renders properly within the popover
                                                                    'allowClear' => true,
                                                                ]
                                                            ],
                                                            'data' => Teachers::getTeachersList($modelTeachersLoad->direction_id),
                                                            'pluginOptions'=>[
                                                                'depends'=>[$modelSubject->id."-".$modelTeachersLoad->id."-direction_id"],
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
                                                    $editable->beforeInput =  \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}][{$modelTeachersLoad->id}]kv-editable-depdrop", '1')
                                                         .

                                                    $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]direction_id")
                                                        ->dropDownList(['' => 'Select Direction...'] + \common\models\guidejob\Direction::getDirectionList(),
                                                        ['id'=> $modelSubject->id."-".$modelTeachersLoad->id."-direction_id"])->label(false) . "\n";

                                                    $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][{$modelTeachersLoad->id}]week_time")->textInput(['placeholder'=>'Enter week time...'])->label(false) . "\n";
                                                    Editable::end();
                                                    ?>
                                                <?php endforeach; ?>
                                                <?php
                                                $modelTeachersLoad = new \common\models\teachers\TeachersLoad();
                                                $editable = Editable::begin([
                                                    'model' => $modelTeachersLoad,
                                                    'attribute' => "[{$modelSubject->id}][0]teachers_id",
                                                    'header' => 'нагрузку',
                                                    'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                    'asPopover' => true,
                                                    'size' => PopoverX::SIZE_MEDIUM,
                                                    'valueIfNull' => false,
                                                    'defaultEditableBtnIcon' => '<i class="glyphicon glyphicon-plus"></i>',
                                                    'format' => Editable::FORMAT_BUTTON,
                                                    'inputType' => Editable::INPUT_DEPDROP,
                                                    'options' => [
                                                        'id' => $modelSubject->id,
                                                        'type' => DepDrop::TYPE_SELECT2,
                                                        'options' => ['placeholder' => 'Select teachers...'],
                                                        'select2Options' => [
                                                            'pluginOptions' => [
                                                                'dropdownParent' => "#".$modelSubject->id."-popover", // set this to "#<EDITABLE_ID>-popover" to ensure select2 renders properly within the popover
                                                                'allowClear' => true,
                                                            ]
                                                        ],
                                                        'pluginOptions'=>[
                                                            'depends'=>[$modelSubject->id."-direction_id"],
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
                                                    ]
                                                ]);
                                                $form = $editable->getForm();
                                                $editable->beforeInput =  \artsoft\helpers\Html::hiddenInput("[{$modelSubject->id}]kv-editable-depdrop", '1')
                                                    .

                                                    $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]direction_id")
                                                        ->dropDownList(['' => 'Select Direction...'] + \common\models\guidejob\Direction::getDirectionList(),
                                                            ['id'=> $modelSubject->id."-direction_id"])->label(false) . "\n";

                                                $editable->afterInput = $form->field($modelTeachersLoad, "[{$modelSubject->id}][0]week_time")->textInput(['placeholder'=>'Enter week time...'])->label(false) . "\n";
                                                Editable::end();
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



