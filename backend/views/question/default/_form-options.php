<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsQuestionOptions */
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
    'model' => $modelsQuestionOptions[0],
    'formId' => 'question-form',
    'formFields' => [
        'name',
        'free_flag',
    ],
]);
?>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
            <th class="text-center">Название</th>
            <th class="text-center">Свободная строка</th>
        <th class="text-center">
            <?php if (!$readonly): ?>
                <button type="button" class="add-time btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            <?php endif; ?>
        </th>
    </tr>
    </thead>
    <tbody class="container-time">
    <?php foreach ($modelsQuestionOptions as $indexTime => $modelQuestionOptions): ?>
        <tr class="room-item">
            <?php
            // necessary for update action.
            if (!$modelQuestionOptions->isNewRecord) {
                echo Html::activeHiddenInput($modelQuestionOptions, "[{$index}][{$indexTime}]id");
            }
            ?>

            <td>
                <?php
                $field = $form->field($modelQuestionOptions, "[{$index}][{$indexTime}]name");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= Html::activeTextInput($modelQuestionOptions, "[{$index}][{$indexTime}]name", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
            </td>
            <td>
                <?php
                $field = $form->field($modelQuestionOptions, "[{$index}][{$indexTime}]free_flag");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= Html::activeCheckbox($modelQuestionOptions, "[{$index}][{$indexTime}]free_flag"); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
            </td>

            <td class="vcenter">
                <?php if (!$readonly): ?>
                    <button type="button" class="remove-time btn btn-danger btn-xs"><span
                                class="fa fa-minus"></span></button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php DynamicFormWidget::end(); ?>
