<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\education\LessonMark;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems */
/* @var $subject_key */
/* @var $timestamp_in */
/* @var $modelTeachers */

$mark_list = LessonMark::getMarkLabelForStudent([LessonMark::MARK,LessonMark::OFFSET_NONOFFSET,LessonMark::REASON_ABSENCE]);
$keyArray = explode('||', $subject_key);
$subject_sect_studyplan_id = $keyArray[0];
$plan_year = $keyArray[1];
$studentFioList = RefBook::find('studyplan_subject-student_fio')->getList();
?>
<div class="lesson-items-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'lesson-items-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-heading">
                Промежуточная аттестация: <?php echo RefBook::find('sect_name_2')->getValue($subject_sect_studyplan_id); ?> за <?php echo $plan_year . '/' . ($plan_year + 1); ?> учебный год.

            </div>
        </div>
        <div class="panel-body">
            <div class="panel">
                <div class="panel-body">
                    <div class="panel">
                        <?php if (!empty($modelsItems)): ?>
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Список оценок промежуточной аттестации
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center">№</th>
                                            <th class="text-center">Ученик</th>
                                            <th class="text-center">Оценка</th>
                                            <th class="text-center">Примечание</th>
                                        </tr>
                                        </thead>
                                        <tbody class="container-items">
                                        <?php foreach ($modelsItems as $index => $modelItems): ?>
                                            <tr class="item">
                                                <?php
                                                // necessary for update action.
                                                if (!$modelItems->isNewRecord) {
                                                    echo Html::activeHiddenInput($modelItems, "[{$index}]id");
                                                }
                                                ?>
                                                <td>
                                                    <span class="panel-title-activities"><?= ($index + 1) ?></span>
                                                </td>
                                                <td>
                                                    <?= Html::activeHiddenInput($modelItems, "[{$index}]plan_year"); ?>
                                                    <?= Html::activeHiddenInput($modelItems, "[{$index}]studyplan_subject_id"); ?>
                                                    <?= $studentFioList[$modelItems->studyplan_subject_id]; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = $form->field($modelItems, "[{$index}]lesson_mark_id");
                                                    echo $field->begin();
                                                    ?>
                                                    <div class="col-sm-12">
                                                        <?= \kartik\select2\Select2::widget(
                                                            [
                                                                'model' => $modelItems,
                                                                'attribute' => "[{$index}]lesson_mark_id",
                                                                'data' => $mark_list,
                                                                'options' => [

//                                                                'disabled' => $readonly,
                                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                                ],
                                                                'pluginOptions' => [
                                                                    'allowClear' => true
                                                                ],
                                                            ]
                                                        ) ?>
                                                        <p class="help-block help-block-error"></p>
                                                    </div>
                                                    <?= $field->end(); ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    $field = $form->field($modelItems, "[{$index}]mark_rem");
                                                    echo $field->begin();
                                                    ?>
                                                    <div class="col-sm-12">
                                                        <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]mark_rem", ['class' => 'form-control']); ?>
                                                        <p class="help-block help-block-error"></p>
                                                    </div>
                                                    <?= $field->end(); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?php if (!empty($modelsItems)): ?>
                    <?= \artsoft\helpers\ButtonHelper::exitButton(['/teachers/default/studyplan-progress', 'id' => $modelTeachers->id]);?>
                    <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit');?>
                <?php endif; ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


