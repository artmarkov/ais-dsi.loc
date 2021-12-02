<?php
use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;

/* @var $readonly */
/* @var $modelsSubject */
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
                                        <th class="text-center">Дисциплина</th>
                                        <th class="text-center">Группа</th>
                                        <th class="text-center">Преподаватели</th>
                                        <th class="text-center">Расписание занятий</th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                        <tr class="item">
                                            <td>
                                                <?= RefBook::find('subject_memo_2')->getValue($modelSubject->id ?? null) ?>
                                            </td>
                                            <td>
                                                <?= Editable::widget([
                                                    'name' => 'id_' . $modelSubject->id,
                                                    'value' => $modelSubject->getSubjectSectStudyplan()->id,
                                                    'attribute' => 'id',
                                                    'header' => 'Группа',
                                                    'displayValueConfig'=> $modelSubject->getSubjectSectStudyplanAll() ?? [],
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
