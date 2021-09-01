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
    'id' => 'teachers-efficiency-summary',
    'validateOnBlur' => false,
])
?>
<div class="teachers-efficiency-search">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, "date_in")->widget(DatePicker::class)->label('Дата начала периода'); ?>
            <?= $form->field($model_date, "date_out")->widget(DatePicker::class)->label('Дата окончания периода'); ?>
            <?= $form->field($model_date, "hidden_flag")->checkbox(['value' => true])->label('Скрыть пустые строки'); ?>
            <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>