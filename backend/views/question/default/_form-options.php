<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsQuestionOptions */
/* @var $model */
/* @var $index */
/* @var $readonly */

?>
<div class="questionForm_<?=$index?>">
    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_inner',
        'widgetBody' => '.container-time',
        'widgetItem' => '.room-item',
        'limit' => 20,
        'min' => ($model->type_id == \common\models\question\QuestionAttribute::TYPE_RADIOLIST || $model->type_id == \common\models\question\QuestionAttribute::TYPE_CHECKLIST) ? 1:0,
        'insertButton' => '.add-time',
        'deleteButton' => '.remove-time',
        'model' => $modelsQuestionOptions[0],
        'formId' => 'question-form',
        'formFields' => [
            'name',
            'free_flag',
        ],
    ]); ?>

    <div class="col-sm-3">
        <label class="control-label"> Опции атрибута</label>
    </div>
    <div class="col-sm-9">
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center">
                    <?php if (!$readonly): ?>
                        <button type="button" class="add-time btn btn-success btn-xs"><span
                                    class="fa fa-plus"></span></button>
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
                        <?= $form->field($modelQuestionOptions, "[{$index}][{$indexTime}]name")->textInput() ?>
                    </td>
                    <td>
                        <?= $form->field($modelQuestionOptions, "[{$index}][{$indexTime}]free_flag")->checkbox() ?>
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
    </div>
    <?php DynamicFormWidget::end(); ?>
</div>