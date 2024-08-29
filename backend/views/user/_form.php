<?php

use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserCommon */
/* @var $readonly */
?>
<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'status')->dropDownList(UserCommon::getStatusList(), ['disabled' => $readonly]) ?>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        Основные сведения
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124])->hint('Важно: Поле необходимо заполнить как в документе. При отсутствии Отчества заполнение не требуется.') ?>
                <?= $form->field($model, 'gender')->dropDownList(UserCommon::getGenderList(), ['disabled' => false]) ?>
                <?= $form->field($model, 'birth_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => false]); ?>
                <?= $form->field($model, 'snils')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                <?= $form->field($model, 'address')->textInput(['maxlength' => 1024]) ?>
                <?= $form->field($model, 'phone')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                <?= $form->field($model, 'phone_optional')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => 124]) ?>
            </div>
        </div>
    </div>
</div>
