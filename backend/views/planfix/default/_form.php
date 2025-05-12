<?php

use artsoft\widgets\ActiveForm;
use common\models\planfix\Planfix;
use artsoft\helpers\Html;
use artsoft\models\User;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\planfix\Planfix */
/* @var $form artsoft\widgets\ActiveForm */

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-items").each(function(index) {
        jQuery(this).html("Этап: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-items").each(function(index) {
        jQuery(this).html("Этап: " + (index + 1))
    });
});


JS;

$this->registerJs($js);
?>

<div class="planfix-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'planfix-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Сведения о работе
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'category_id')->dropDownList(\common\models\planfix\PlanfixCategory::getPlanfixCategoryList(), ['prompt' => '', 'encodeSpaces' => true, 'disabled' => !$model->isNewRecord ? true : $readonly]) ?>

                    <?= $form->field($model->loadDefaultValues(), 'planfix_author')->widget(\kartik\select2\Select2::class, [
                        'data' => User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Authors...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],

                    ]);
                    ?>
                    <?= $form->field($model->loadDefaultValues(), 'executors_list')->widget(\kartik\select2\Select2::class, [
                        'data' => User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],

                    ]);
                    ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

                    <?= $form->field($model, 'importance')->dropDownList(Planfix::getImportanceList(), [
//                        'options' => [
//                            Planfix::IMPORTANCE_NORM => ['selected' => true]
//                        ]
                    ]) ?>
                    <?= $form->field($model, 'planfix_date')->widget(\kartik\date\DatePicker::class, ['disabled' => $readonly])->textInput(['autocomplete' => 'off']); ?>

                    <?= $form->field($model, 'status')->dropDownList(Planfix::getStatusList(), ['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'status_reason')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Загруженные материалы для работы
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => $readonly]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 10, // the maximum times, an element can be added (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsItems[0],
                'formId' => 'planfix-form',
                'formFields' => [
                    'planfix_activity_category',
                    'executor_comment',
                    'author_comment',
                    'ctivity_status',
                    'activity_status_reason',
                ],
            ]); ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Этапы работы
                </div>
                <div class="panel-body">
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsItems as $index => $modelItems): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-items">Этап: <?= ($index + 1) ?></span>
                                    <?php if (!$readonly): ?>
                                        <div class="pull-right">
                                            <button type="button" class="remove-item btn btn-default btn-xs">удалить
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                        <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelItems->isNewRecord) {
                                        echo Html::activeHiddenInput($modelItems, "[{$index}]id");
                                    }
                                    ?>
                                    <?= $form->field($modelItems, "[{$index}]planfix_activity_category")->dropDownList(\common\models\planfix\PlanfixActivity::getActivityCategoryList(), ['disabled' => $readonly]) ?>
                                    <?= $form->field($modelItems, "[{$index}]executor_comment")->textarea(['rows' => '3', 'maxlength' => true]) ?>
                                    <?= $form->field($modelItems, "[{$index}]author_comment")->textarea(['rows' => '3', 'maxlength' => true]) ?>
                                    <?= $form->field($modelItems, "[{$index}]activity_status")->dropDownList(\common\models\planfix\PlanfixActivity::getStatusActivityList(), ['disabled' => $readonly]) ?>
                                    <?= $form->field($modelItems, "[{$index}]activity_status_reason")->textInput(['maxlength' => true]) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- .panel -->
                <div class="panel-footer">
                    <?php if (!$readonly): ?>
                        <div class="form-group btn-group">
                            <button type="button" class="add-item btn btn-success btn-sm pull-right"><i
                                        class="glyphicon glyphicon-plus"></i> Добавить
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
                <?php endif; ?>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
