<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'cheet-search',
    'validateOnBlur' => false,
])
?>
<div class="cheet-search">
    <div class="panel panel-default">
        <div class="panel-body">

            <?= $form->field($model_date, "date_in")->widget(DatePicker::class)->label('Дата начала периода'); ?>

            <?= $form->field($model_date, "date_out")->widget(DatePicker::class)->label('Дата окончания периода'); ?>

            <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>