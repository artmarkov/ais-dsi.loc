<?php

use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $model common\models\employees\Employees */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $userCommon common\models\user\UserCommon */
/* @var $readonly */
?>

<div class="employees-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'student-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>

    <div class="panel">
        <div class="panel-heading">
            Информация о сотруднике
            <?php if (!$userCommon->isNewRecord):?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/students/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($userCommon, 'status')->dropDownList(UserCommon::getStatusList()) ?>

                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Основные сведения
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($userCommon, 'last_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($userCommon, 'first_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($userCommon, 'middle_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($userCommon, 'gender')->dropDownList(UserCommon::getGenderList()) ?>
                            <?= $form->field($userCommon, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()); ?>
                            <?= $form->field($userCommon, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                            <?= $form->field($userCommon, 'address')->textInput(['maxlength' => 1024]) ?>
                            <?= $form->field($userCommon, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?= $form->field($userCommon, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Служебные данные
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--            --><?php //DynamicFormWidget::begin([
            //                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            //                'widgetBody' => '.container-items', // required: css class selector
            //                'widgetItem' => '.item', // required: css class
            //                'limit' => 4, // the maximum times, an element can be added (default 999)
            //                'min' => 1, // 0 or 1 (default 1)
            //                'insertButton' => '.add-item', // css class
            //                'deleteButton' => '.remove-item', // css class
            //                'model' => $modelsRelations[0],
            //                'formId' => 'student-form',
            //                'formFields' => [
            //                    'work_id',
            //                    'direction_id',
            //                    'stake_id',
            //                ],
            //            ]); ?>

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
