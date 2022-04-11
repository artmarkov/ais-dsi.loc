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
    'id' => 'attendlog-search',
    'validateOnBlur' => false,
])
?>
<div class="attendlog-search">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'date')->widget(DatePicker::class)->label('Дата'); ?>

            <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
            <?= \artsoft\helpers\ButtonHelper::createButton(['/service/attendlog/find']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>