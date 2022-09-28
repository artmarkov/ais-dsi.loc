<?php

use artsoft\helpers\RefBook;
use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsTest */
/* @var $model */
/* @var $index */
/* @var $readonly */

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-time',
    'widgetItem' => '.room-item',
    'limit' => 8,
    'min' => 1,
    'insertButton' => '.add-time',
    'deleteButton' => '.remove-time',
    'model' => $modelsTest[0],
    'formId' => 'applicants-form',
    'formFields' => [
        'entrant_members_id',
        'entrant_test_id',
        'entrant_mark_id',
    ],
]); ?>

<div class="container-time">
    <?php foreach ($modelsTest as $indexTest => $modelTest): ?>
        <div class="room-item">
            <?php
            // necessary for update action.
            if (!$modelTest->isNewRecord) {
                echo Html::activeHiddenInput($modelTest, "[{$index}][{$indexTest}]id");
            }
            echo Html::activeHiddenInput($modelTest, "[{$index}][{$indexTest}]entrant_members_id");
            echo Html::activeHiddenInput($modelTest, "[{$index}][{$indexTest}]entrant_test_id");
            ?>
            <td>
                <?php
                $field = $form->field($modelTest, "[{$index}][{$indexTest}]entrant_mark_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\select2\Select2::widget(
                        [
                            'model' => $modelTest,
                            'attribute' => "[{$index}][{$indexTest}]entrant_mark_id",
                            'data' => \common\models\education\LessonMark::getMarkLabelForEntrant(),
                            'options' => [
                                    'disabled' => $readonly,
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
        </div>
    <?php endforeach; ?>
</div>


<?php DynamicFormWidget::end(); ?>
