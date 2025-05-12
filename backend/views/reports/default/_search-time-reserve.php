<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanStat;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'time-reserve-search-form',
    'validateOnBlur' => false,
])
?>
<div class="studyplan-stat-search-form">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                [
                    'disabled' => false,
//                    'onchange'=>'js: $(this).closest("form").submit()',
                ])->label(Yii::t('art/studyplan', 'Plan Year'));
            ?>
            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>