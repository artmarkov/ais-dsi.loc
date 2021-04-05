<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */
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
            <?php if (!$userCommon->isNewRecord):?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/teachers/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($userCommon, 'status')->dropDownList(UserCommon::getStatusList()) ?>
                    <?= $form->field($model, 'position_id')->dropDownList(\common\models\student\StudentPosition::getPositionList(), [
                        'prompt' => Yii::t('art/student', 'Select Position...'),
                        'id' => 'position_id'
                    ])->label(Yii::t('art/student', 'Name Position'));
                    ?>
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
                            <?= $form->field($userCommon, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?= $form->field($userCommon, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Документ
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'sert_name')->dropDownList(\common\models\student\Student::STUDENT_DOC, [
                                'options' => [
                                    'birth_cert' => ['selected' => true]
                                ]
                            ]) ?>

                            <?= $form->field($model, 'sert_series')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sert_num')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sert_organ')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()); ?>

                        </div>
                    </div>
                </div>
            </div>
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
