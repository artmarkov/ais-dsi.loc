<?php

use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\weeklyscheduler\WeeklyScheduler;

/* @var $readonly */
/* @var $modelsSubject */

$JSInit = <<<EOF
    function(e,f) {
    console.log(e);
    console.log(f);
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
                                                'name' => 'teachers_load_' . $modelTeachersLoad->teachers_id,
                                                'value' => $modelTeachersLoad->teachers_id,
                                                'attribute' => 'id',
                                                'header' => 'Нагрузку',
                                                'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                'asPopover' => true,
                                                'format' => Editable::FORMAT_LINK,
                                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                'data' => $modelSubject->getSubjectTeachers(),
                                                'options' => ['class' => 'form-control'],
                                                'formOptions' => [
                                                    'action' => Url::toRoute([
                                                        '/teachers/default/set-load',
                                                        'studyplan_subject_id' => $modelSubject->id ?? null,
                                                        'subject_sect_studyplan_id' => $modelSubject->getSubjectSectStudyplan()->id ?? null
                                                    ]),
                                                ],
                                            ]);
                                            ?>
                                            <?php endforeach;?>
                                            <?= Editable::widget([
                                                'name' => 'teachers_load_0',
                                                'value' => '',
                                                'attribute' => 'id',
                                                'header' => 'Нагрузку',
                                                'displayValueConfig' => RefBook::find('teachers_fio')->getList(),
                                                'asPopover' => true,
                                                'format' => Editable::FORMAT_LINK,
                                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                'data' => $modelSubject->getSubjectTeachers(),
                                                'options' => ['class' => 'form-control'],
                                                'formOptions' => [
                                                    'action' => Url::toRoute([
                                                        '/teachers/default/set-load',
                                                        'studyplan_subject_id' =>  null,
                                                        'subject_sect_studyplan_id' => $modelSubject->getSubjectSectStudyplan()->id ?? null
                                                    ]),
                                                ],
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




