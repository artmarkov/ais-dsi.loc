<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\education\LessonMark;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelStudyplan */
/* @var $subject_key */

$mark_list = LessonMark::getMarkLabelForStudent([LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);
$keyArray = explode('||', $subject_key);
$studyplan_subject_id = $keyArray[0];
$plan_year = $keyArray[1];

//print_r($subject); die();
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
                Промежуточная аттестация:
                <?php echo RefBook::find('subject_memo_4')->getValue($studyplan_subject_id); ?>
                за <?php echo $plan_year . '/' . ($plan_year + 1); ?> учебный год.

                <?php if (!$model->isNewRecord && \artsoft\Art::isBackend()): ?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="panel">
                <div class="panel-body">
                    <div class="panel">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Оценка промежуточной аттестации
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
                                    <tr class="item">
                                        <?php
                                        // necessary for update action.
                                        if (!$model->isNewRecord) {
                                            echo Html::activeHiddenInput($model, "id");
                                        }
                                        ?>
                                        <td>
                                            <span class="panel-title-activities"><?= 1 ?></span>
                                        </td>
                                        <td>
                                            <?= Html::activeHiddenInput($model, "plan_year"); ?>
                                            <?= Html::activeHiddenInput($model, "studyplan_subject_id"); ?>
                                            <?= RefBook::find('studyplan_subject-student_fio')->getValue($model->studyplan_subject_id); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $field = $form->field($model, "lesson_mark_id");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \kartik\select2\Select2::widget(
                                                    [
                                                        'model' => $model,
                                                        'attribute' => "lesson_mark_id",
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
                                            $field = $form->field($model, "mark_rem");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \yii\helpers\Html::activeTextInput($model, "mark_rem", ['class' => 'form-control']); ?>
                                                <p class="help-block help-block-error"></p>
                                            </div>
                                            <?= $field->end(); ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::exitButton(['/studyplan/default/studyplan-progress', 'id' => $modelStudyplan->id]); ?>
                    <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit'); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


