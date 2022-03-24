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
    'id' => 'plan_year-search',
    'validateOnBlur' => false,
])
?>
<div class="plan_year-search">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList())->label('Учебный год');
            ?>
            <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>