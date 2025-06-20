<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use common\models\education\LessonMark;


/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems */
/* @var $studyplan_subject_id */
/* @var $subject_sect_studyplan_id */

$models_sch = \common\models\schedule\SubjectSchedule::getSchedule($model->subject_sect_studyplan_id, $model->studyplan_subject_id);
$mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE,LessonMark::MARK,LessonMark::OFFSET_NONOFFSET,LessonMark::REASON_ABSENCE]);

if ($model->subject_sect_studyplan_id != 0) {
    $studyplanSubjectList = \common\models\subjectsect\SubjectSectStudyplan::findOne($model->subject_sect_studyplan_id)->studyplan_subject_list;
} else {
    $studyplanSubjectList = $model->studyplan_subject_id;
}
$modelsStudent = (new \yii\db\Query())->select('studyplan_subject_id,education_cat_id,student_fio')->from('studyplan_subject_view')
    ->where(new \yii\db\Expression("studyplan_subject_id = any (string_to_array('{$studyplanSubjectList}', ',')::int[])"))
    ->all();
$programm_cat_list = \yii\helpers\ArrayHelper::getColumn($modelsStudent, 'education_cat_id');
$modelsStudent = \yii\helpers\ArrayHelper::index($modelsStudent, 'studyplan_subject_id');
$division_list = \common\models\education\EducationCat::find()->select('division_list')
    ->where(['id' => $programm_cat_list])
    ->column();
//print_r($division_list);die();
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
                <?php foreach ($models_sch as $itm => $m): ?>
                    <?php
                    $string = ' ' . \artsoft\helpers\ArtHelper::getWeekValue('short', $m->week_num);
                    $string .= ' ' . \artsoft\helpers\ArtHelper::getWeekdayValue('short', $m->week_day) . ' ' . $m->time_in . '-' . $m->time_out;

                    ?>
                    <tr class="item">
                        <td>
                            <span class="panel-title"><?= $itm + 1 ?></span>
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
                            'data' => \common\models\education\LessonTest::getLessonTestList($model,[1,2], $division_list),
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
                        if ($model->lesson_date) {
                            echo Html::activeHiddenInput($model, 'lesson_date');
                        }
                        ?>
                        <?= $form->field($model, 'lesson_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['options' => ['disabled' => false/*$model->lesson_date*/]]); ?>
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
                            <?php if (isset($modelsStudent[$modelItems->studyplan_subject_id])): ?>
                                <tr class="item">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelItems->isNewRecord) {
                                        echo Html::activeHiddenInput($modelItems, "[{$index}]id");
                                    }
                                    ?>
                                    <?php
                                        echo Html::activeHiddenInput($modelItems, "[{$index}]lesson_items_id");
                                    ?>
                                    <td>
                                        <span class="panel-title-activities"><?= $index + 1 ?></span>
                                    </td>
                                    <td>
                                        <?= Html::activeHiddenInput($modelItems, "[{$index}]studyplan_subject_id"); ?>
                                        <?= isset($modelsStudent[$modelItems->studyplan_subject_id]) ? $modelsStudent[$modelItems->studyplan_subject_id]['student_fio'] : ''; ?>
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
                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::exitButton();?>
                    <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit');?>
               <!-- <?php /*if ($model->lesson_date && !empty($modelsItems)): */?>
                <?php /*else: */?>
                    <?/*= \artsoft\helpers\ButtonHelper::exitButton();*/?>
                    <?/*= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Продолжить', ['class' => 'btn btn-md btn-info', 'name' => 'submitAction', 'value' => 'next']); */?>
                --><?php /*endif; */?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


