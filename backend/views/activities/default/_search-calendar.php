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
    'id' => 'calendar',
    'validateOnBlur' => false,
])
?>
<div class="calendar-search">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'auditory_id')->dropDownList(\artsoft\helpers\RefBook::find('auditory_memo_1', 1, true)->getList(),
                [
                    'disabled' => false,
                    'onchange'=>'js: $(this).closest("form").submit()',
                ])->label('Аудитория');
            ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>