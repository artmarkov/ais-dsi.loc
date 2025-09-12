<?php

use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */
/* @var $readonly */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        Сведения о пропуске
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">

                <?= $form->field($model, 'key_hex')->textInput(['maxlength' => true,'disabled' => $readonly]) ?>

                <?= $form->field($model, 'timestamp_deny')->widget(DateTimePicker::class, ['disabled' => $readonly]); ?>

                <?= $form->field($model, 'social_card_flag')->checkbox(['disabled' => $readonly]) ?>
            </div>
        </div>
    </div>
</div>
