<?php

use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\sigur\UsersCard */
/* @var $readonly */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        Сведения о пропуске
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'key_hex')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'timestamp_deny')->widget(DateTimePicker::class, ['disabled' => $readonly]); ?>
            </div>
        </div>
    </div>
</div>