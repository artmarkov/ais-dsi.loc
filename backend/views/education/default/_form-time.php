<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-time',
    'widgetItem' => '.room-item',
    'limit' => 4,
    'min' => 1,
    'insertButton' => '.add-time',
    'deleteButton' => '.remove-time',
    'model' => $modelsTime[0],
    'formId' => 'education-programm-form',
    'formFields' => [
        'cource',
        'week_time',
        'year_time'
    ],
]); ?>
<table class="table">
    <thead>
    <tr>
        <th class="text-center">Cource</th>
        <th class="text-center">Week Time</th>
        <th class="text-center">Year Time</th>
        <th class="text-center">
            <button type="button" class="add-time btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
        </th>
    </tr>
    </thead>
    <tbody class="container-time">
    <?php foreach ($modelsTime as $indexTime => $modelTime): ?>
        <tr class="room-item">
            <?php
            // necessary for update action.
            if (!$modelTime->isNewRecord) {
                echo Html::activeHiddenInput($modelTime, "[{$index}][{$indexTime}]id");
            }
            ?>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]cource")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]week_time")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]year_time")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td class="vcenter">
                <button type="button" class="remove-time btn btn-danger btn-xs"><span
                            class="fa fa-minus"></span></button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php DynamicFormWidget::end(); ?>
