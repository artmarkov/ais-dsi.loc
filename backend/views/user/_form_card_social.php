<?php

use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */
/* @var $readonly */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        Сведения о картах
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'social_card_flag')->checkbox(['disabled' => $readonly]) ?>
            </div>
        </div>
    </div>
</div>
