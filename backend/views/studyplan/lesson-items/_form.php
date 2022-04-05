<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\LessonProgress;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems artsoft\widgets\$modelsItems */
/* @var $studyplan_subject_id */
/* @var $subject_sect_studyplan_id */

$models_sch = \common\models\schedule\SubjectSchedule::getSchedule($model->subject_sect_studyplan_id, $model->studyplan_subject_id);

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
                <?php echo RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id); ?>
                <?php echo RefBook::find('sect_name_2')->getValue($model->subject_sect_studyplan_id); ?>

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
                    <th class="text-center">Расписание</th>
                    <th class="text-center">Аудитория</th>
                </tr>
                </thead>
                <tbody class="container-items">
                <?php foreach ($models_sch as $index => $id): ?>
                    <?php $m = \common\models\schedule\SubjectSchedule::findOne($id); ?>
                    <?php
                    $string = ' ' . \artsoft\helpers\ArtHelper::getWeekValue('short', $m->week_num);
                    $string .= ' ' . \artsoft\helpers\ArtHelper::getWeekdayValue('short', $m->week_day) . ' ' . $m->time_in . '-' . $m->time_out;

                    ?>
                    <tr class="item">
                        <td>
                            <span class="panel-title"><?= $index + 1 ?></span>
                        </td>
                        <td>
                            <span class="panel-title"><?= $string ?></span>
                        </td>
                        <td>
                            <span class="panel-title"><?= RefBook::find('auditory_memo_1')->getValue($m->auditory_id) ?></span>
                        </td>
                    <tr/>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
                        <?= $form->field($model, 'lesson_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, [/*'disabled' => $readonly*/]); ?>
                        <?= $form->field($model, 'lesson_topic')->textInput() ?>
                        <?= $form->field($model, 'lesson_rem')->textInput() ?>
                    </div>
                </div>
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
                                        <?= LessonProgress::getStudentName($modelItems->studyplan_subject_id); ?>
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
                                                    'data' => RefBook::find('lesson_mark')->getList(),
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
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


