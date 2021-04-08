<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $model common\models\students\Student */
/* @var $userCommon \common\models\user\UserCommon */
/* @var $readonly */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="student-form">

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
            Информация об ученике
            <?php if (!$userCommon->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/students/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <?= $form->field($model, 'position_id')->dropDownList(\common\models\students\StudentPosition::getPositionList(), [
                'prompt' => Yii::t('art/student', 'Select Position...'),
                'id' => 'position_id',
                'disabled' => $readonly
            ])->label(Yii::t('art/student', 'Position'));
            ?>

            <?= $this->render('/user/_form', ['form' => $form, 'model' => $userCommon, 'readonly' => $readonly]) ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Документ
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'sert_name')->dropDownList(\common\models\students\Student::STUDENT_DOC, [
                                'disabled' => $readonly,
                                'options' => [
                                    'birth_cert' => ['selected' => true]
                                ]
                            ]) ?>
                            <?= $form->field($model, 'sert_series')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_num')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_organ')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname(), ['disabled' => $readonly]); ?>
                            <?php if (!$model->isNewRecord) : ?>
                                <div class="form-group field-student-attachment">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="student-attachment">Скан документа</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'pluginOptions' => ['theme' => 'explorer'], 'disabled' => $readonly]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

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
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Сведения о родителях
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                        </div>
                    </div>
                </div>
            </div>
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
