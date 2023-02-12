<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use kartik\sortinput\SortableInput;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\subjectsect\SubjectSect */
/* @var $model_date */
/* @var $modelsSubjectSectStudyplan \common\models\subjectsect\SubjectSectStudyplan */

$this->title = Yii::t('art/guide', 'Distribution');
$this->params['breadcrumbs'][] = $this->title;

$sub_group_qty = $model->sub_group_qty;
$course_list = $model->course_list;
$class_index = $model->class_index;
$course_flag = $model->course_flag;
$group = 0;
?>
<div class="subject-sect-form">

    <div class="panel">
        <div class="panel-heading">
            Распределение по группам <?= RefBook::find('subject_name')->getValue($model->subject_id); ?>
            <?= $this->render('_search', compact('model_date')) ?>
        </div>
        <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
                'inputOptions' => ['readonly' => $readonly]
            ],
            'id' => 'subject-sect-form',
            'validateOnBlur' => false,
        ]);

        ?>
        <?php if ($course_flag) : ?>
            <div class="panel-body">
                <div class="row">
                    <div class="table-responsive kv-grid-container">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-warning">
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;min-width: 100px;">
                                    Группа
                                </th>
                                <th colspan="<?= count($course_list) ?>" class="text-center" style="min-width: 100px">
                                    Годы обучения
                                </th>
                            </tr>
                            <tr>
                                <?php for ($i = 1; $i <= count($course_list); $i++): ?>
                                    <th class="text-center" style="min-width: 300px;"><?= $course_list[$i - 1] ?></th>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody class="container-items">
                            <?php foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan): ?>
                                <?php
                                if ($index == count($course_list) * $group) {
                                    $group++;
                                    $class_name = $class_index != '' ? sprintf('%s %s-%02d', $model->sect_name, $class_index, $group) : sprintf('%s %02d', $model->sect_name, $group);
                                    echo '<tr>
                                <td class="text-center" style="vertical-align: middle;">' . $class_name . '</td>';
                                }
                                echo '<td>';
                                echo RefBook::find('sect_memo_2')->getValue($modelSubjectSectStudyplan->id);

                                // necessary for update action.
                                if (!$modelSubjectSectStudyplan->isNewRecord) {
                                    echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]id");
                                }
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_sect_id");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]group_num");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]plan_year");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]course");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_type_id");
                                echo SortableInput::widget([
                                    'model' => $modelSubjectSectStudyplan,
                                    'attribute' => "[{$index}]studyplan_subject_list",
                                    'hideInput' => true,
                                    'sortableOptions' => [
                                        'itemOptions' => ['class' => 'alert alert-success'],
                                        'options' => ['style' => 'min-height: 40px'],
                                        'connected' => true,
                                    ],
                                    'options' => ['class' => 'form-control', 'readonly' => true],
                                    'delimiter' => ',',
                                    'items' => $modelSubjectSectStudyplan->getSubjectSectStudyplans($readonly),
                                ]);

                                echo '<p class="help-block help-block-error"></p>
                                    </td>';
                                if ($index == count($course_list) * $group) {
                                    echo '</tr>';
                                }
                                ?>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <?php for ($i = 1; $i <= count($course_list); $i++): ?>
                                    <td>
                                        <?= SortableInput::widget([
                                            'name' => "[{$i}]studyplan",
                                            'items' => $model->getStudyplanForProgramms($model_date->plan_year, $course_list[$i - 1], $readonly),
                                            'hideInput' => true,
                                            'sortableOptions' => [
                                                'itemOptions' => ['class' => 'alert alert-info'],
                                                'options' => ['style' => 'min-height: 40px'],
                                                'connected' => true,
                                            ],
                                            'options' => ['class' => 'form-control', 'readonly' => true]
                                        ]);
                                        ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="panel-body">
                <div class="row">
                    <div class="table-responsive kv-grid-container">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-warning">
                            <tr>
                                <th class="text-center" style="vertical-align: middle; min-width: 100px; width: 40%;">
                                    Группа
                                </th>
                                <th class="text-center" style="min-width: 100px">
                                    Ученики группы
                                </th>
                            </tr>
                            </thead>
                            <tbody class="container-items">
                            <?php foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan): ?>
                                <?php
                                $class_name = RefBook::find('sect_memo_2')->getValue($modelSubjectSectStudyplan->id);
                                echo '<tr>
                                <td class="text-center" style="vertical-align: middle;">' . $class_name . '</td>';

                                echo '<td>';
                                // necessary for update action.
                                if (!$modelSubjectSectStudyplan->isNewRecord) {
                                    echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]id");
                                }
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_sect_id");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]group_num");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]plan_year");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]course");
                                echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]subject_type_id");
                                echo SortableInput::widget([
                                    'model' => $modelSubjectSectStudyplan,
                                    'attribute' => "[{$index}]studyplan_subject_list",
                                    'hideInput' => true,
                                    'sortableOptions' => [
                                        'itemOptions' => ['class' => 'alert alert-success'],
                                        'options' => ['style' => 'min-height: 40px'],
                                        'connected' => true,
                                    ],
                                    'options' => ['class' => 'form-control', 'readonly' => true],
                                    'delimiter' => ',',
                                    'items' => $modelSubjectSectStudyplan->getSubjectSectStudyplans($readonly),
                                ]);

                                echo '<p class="help-block help-block-error"></p>
                                    </td>';
                                echo '</tr>';
                                ?>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td>
                                    <?= SortableInput::widget([
                                        'name' => "[0]studyplan",
                                        'items' => $model->getStudyplanForProgramms($model_date->plan_year, null, $readonly),
                                        'hideInput' => true,
                                        'sortableOptions' => [
                                            'itemOptions' => ['class' => 'alert alert-info'],
                                            'options' => ['style' => 'min-height: 40px'],
                                            'connected' => true,
                                        ],
                                        'options' => ['class' => 'form-control', 'readonly' => true]
                                    ]);
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                            <?php
                            //  echo '<pre>' . print_r($model_date, true) . '</pre>';

                            ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::saveButton() : \artsoft\helpers\ButtonHelper::updateButton($model); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


