<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\reports\CreativeStat;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-stat-search-form',
    'validateOnBlur' => false,
])
?>
<div class="studyplan-stat-search-form">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'options')->checkboxList( CreativeStat::OPTIONS_FIELDS, ['value' => CreativeStat::OPTIONS_FIELDS_DEFAULT])->label('Поля для выгрузки');
            ?>
            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>