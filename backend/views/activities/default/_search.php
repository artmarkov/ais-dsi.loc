<?php

use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'activities-search',
    'validateOnBlur' => false,
])
?>
<div class="activities-search">
    <div class="panel">
        <div class="panel-body">
            <?= $form->field($model_date, "date")->widget(DatePicker::class, [
                    'pluginEvents' => ['changeDate' => "function(e){
                                           $(e.target).closest('form').submit();
                                        }" ]
            ])->label('Дата');
            ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

