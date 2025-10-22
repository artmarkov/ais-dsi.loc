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
                <?= $form->field($model, 'social_card_flag')->radioList([1 => 'Карта в наличии', 2 => 'Карты нет'], ['disabled' => $readonly]) ?>
            </div>
        </div>
    </div>
</div>
