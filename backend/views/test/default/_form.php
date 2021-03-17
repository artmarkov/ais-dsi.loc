<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Адрес: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Адрес: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>

<div class="panel">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'dynamic-form']); ?>
    <div class="panel-heading">
        Информация о сотруднике
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($modelCustomer, 'first_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($modelCustomer, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>

        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 4, // the maximum times, an element can be added (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelsAddress[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'full_name',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
            ],
        ]); ?>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-envelope"></i> Адреса

            </div>
            <div class="panel-body">
                <div class="container-items"><!-- widgetBody -->
                    <?php foreach ($modelsAddress as $index => $modelAddress): ?>
                        <div class="item panel panel-info"><!-- widgetItem -->
                            <div class="panel-heading">
                                <span class="panel-title-address">Адрес: <?= ($index + 1) ?></span>
                                <div class="pull-right">
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                                class="glyphicon glyphicon-trash"></i> удалить</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (!$modelAddress->isNewRecord) {
                                    echo Html::activeHiddenInput($modelAddress, "[{$index}]id");
                                }
                                ?>
                                <?= $form->field($modelAddress, "[{$index}]full_name")->textInput(['maxlength' => true]) ?>
                                <?= $form->field($modelAddress, "[{$index}]address_line1")->textInput(['maxlength' => true]) ?>
                                <?= $form->field($modelAddress, "[{$index}]address_line2")->textInput(['maxlength' => true]) ?>
                                <?= $form->field($modelAddress, "[{$index}]city")->textInput(['maxlength' => true]) ?>
                                <?= $form->field($modelAddress, "[{$index}]state")->textInput(['maxlength' => true]) ?>
                                <?= $form->field($modelAddress, "[{$index}]postal_code")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div><!-- .panel -->
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <button type="button" class="add-item btn btn-success btn-sm pull-right"><i
                                class="glyphicon glyphicon-plus"></i> Добавить
                    </button>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="form-group btn-group">
            <?= \artsoft\helpers\ButtonHelper::submitButtons($modelCustomer); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>