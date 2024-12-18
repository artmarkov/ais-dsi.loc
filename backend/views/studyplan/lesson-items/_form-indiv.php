<?php

use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use common\models\education\LessonItems;
use common\models\education\LessonMark;


/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems */
/* @var $subject_key */
/* @var $timestamp_in */
/* @var $modelTeachers */

$models_sch = \common\models\schedule\SubjectScheduleStudyplanView::getScheduleIndiv($subject_key, $modelTeachers->id, $timestamp_in);
$mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE,LessonMark::MARK,LessonMark::OFFSET_NONOFFSET,LessonMark::REASON_ABSENCE]);
$subject = (new \yii\db\Query())->select('subject')
    ->from('lesson_progress_view')
    ->where(['=', 'subject_key', $subject_key])
    ->scalar();
//print_r($model);
$auditoryList = RefBook::find('auditory_memo_1')->getList();
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
                Посещаемость и успеваемость:
                <?php echo $subject; ?>

                <?php if (!$model->isNewRecord): ?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Ученик</th>
                    <th class="text-center">Расписание</th>
                    <th class="text-center">Аудитория</th>
                </tr>
                </thead>
                <tbody class="container-items">
                <?php foreach ($models_sch as $index => $m): ?>
                    <?php
                    $string = ' ' . \artsoft\helpers\ArtHelper::getWeekValue('short', $m['week_num']);
                    $string .= ' ' . \artsoft\helpers\ArtHelper::getWeekdayValue('short', $m['week_day']) . ' ' . \artsoft\helpers\Schedule::decodeTime($m['time_in']) . '-' . \artsoft\helpers\Schedule::decodeTime($m['time_out']);

                    ?>
                    <tr class="item">
                        <td>
                            <span class="panel-title"><?= $index + 1 ?></span>
                        </td>
                        <td>
                            <span class="panel-title"><?= $m['student_fio'] ?></span>
                        </td>
                        <td>
                            <span class="panel-title"><?= $string ?></span>
                        </td>
                        <td>
                            <span class="panel-title"><?= $auditoryList[$m['auditory_id']] ?? '' ?></span>
                        </td>
                    <tr/>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="panel">
                <div class="panel-body">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($model, "lesson_test_id")->widget(\kartik\select2\Select2::class, [
                                    'data' => \common\models\education\LessonTest::getLessonTestList($model),
                                    'options' => [
//                                'disabled' => $readonly,
                                        'placeholder' => Yii::t('art', 'Select...'),
                                        'multiple' => false,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ]
                                ]);
                                ?>
                                <?php
                                if (!$model->getErrors() && $model->lesson_date) {
                                    echo Html::activeHiddenInput($model, 'lesson_date');
                                }
                                ?>
                                <?= $form->field($model, 'lesson_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['options' => ['disabled' => (!$model->getErrors() && $model->lesson_date)]]); ?>
                                <?= $form->field($model, 'lesson_topic')->textInput() ?>
                                <?= $form->field($model, 'lesson_rem')->textInput() ?>
                            </div>
                        </div>
                        <?php if ($model->lesson_date && !empty($modelsItems)): ?>
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Список оценок
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
                <?php if ($model->lesson_date && !empty($modelsItems)): ?>
                    <?= \artsoft\helpers\ButtonHelper::exitButton();?>
                    <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit');?>
                <?php else: ?>
                   <?= \artsoft\helpers\ButtonHelper::exitButton();?>
                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Продолжить', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'next']); ?>
                <?php endif; ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


