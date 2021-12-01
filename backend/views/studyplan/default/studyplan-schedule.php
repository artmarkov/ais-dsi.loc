<?php

use artsoft\helpers\RefBook;
use kartik\editable\Editable;
use yii\helpers\Url;

?>
<div class="panel">
    <div class="panel-heading">
        Нагрузка преподавателей и расписание занятий
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
                                    <?php foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan): ?>
                                        <tr class="item">
                                            <td>
                                                <?= RefBook::find('subject_memo_2')->getValue($modelSubjectSectStudyplan->id) ?>
                                            </td>
                                            <td>
                                                <?= Editable::widget([
                                                    'model' => $modelSubjectSectStudyplan,
                                                    'attribute' => 'class_name',
                                                    'asPopover' => true,
                                                    'format' => Editable::FORMAT_LINK,
                                                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                                    'data'=> $modelSubjectSectStudyplan->getSubjectSectStudyplanAll(),
//                                                    'inlineSettings' => [
//                                                        'templateAfter' => Editable::INLINE_AFTER_1,
//                                                        'templateBefore' => Editable::INLINE_BEFORE_2,
//                                                    ],
                                                    'options' => ['class' => 'form-control', 'placeholder' => 'Enter name...'],
                                                    'formOptions' => [
                                                        'action' => Url::toRoute(['/studygroups/default/set-group', 'id' => $modelSubjectSectStudyplan->id]),
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