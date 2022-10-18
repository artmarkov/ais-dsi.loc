<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $modelLesson common\models\education\LessonItems */
/* @var $modelProgress common\models\education\LessonProgress */
/* @var $index */

$modelLessonItems = $modelLesson[$index];
$modelProgressItems = $modelProgress[$index];
?>
<div class="lesson-items-protocol-form">
    <div class="panel panel-info">
        <div class="panel-heading">
            Оценка
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($modelLessonItems, "[{$index}]lesson_test_id")->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\education\LessonTest::getLessonTestList($modelLessonItems),
                    'options' => [
                        'disabled' => $readonly,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                ?>
                <?= $form->field($modelLessonItems, "[{$index}]lesson_date")->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonly]); ?>
<?php //$form->field($modelLesson, "[{$index}]lesson_topic")->textInput() ?>
<?php //$form->field($modelLesson, "[{$index}]lesson_rem")->textInput() ?>
                <?php
                // necessary for update action.
                if (!$modelLessonItems->isNewRecord) {
                    echo Html::activeHiddenInput($modelLessonItems, "[{$index}]id");
                }
                ?>
                <?= Html::activeHiddenInput($modelProgressItems, "[{$index}]studyplan_subject_id"); ?>

                <?= $form->field($modelProgressItems, "[{$index}]lesson_mark_id")->widget(\kartik\select2\Select2::class, [
                    'data' => RefBook::find('lesson_mark')->getList(),
                    'showToggleAll' => false,
                    'options' => [
                        'disabled' => $readonly,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],

                ]);

                ?>
<?php //$form->field($modelProgress, "[{$index}]mark_rem")->textInput() ?>

            </div>
        </div>
    </div>
</div>


