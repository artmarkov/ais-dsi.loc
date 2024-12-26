<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'student-history-search-form',
    'validateOnBlur' => false,
])
?>
<div class="student-history-search-form">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                [
                    'disabled' => false,
                    'onchange'=>'js: $(this).closest("form").submit()',
                ])->label(Yii::t('art/studyplan', 'Plan Year'));
            ?>
            <?= $form->field($model_date, 'studyplan_id')->widget(\kartik\select2\Select2::class, [
                'data' => StudyplanView::getStudyplanListByPlanYear($model_date->plan_year, false),
                'options' => [
                    'onchange' => 'js: $(this).closest("form").submit()',
                    'placeholder' => Yii::t('art', 'Select...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Учебный план');
            ?>
            </div>
    </div>
</div>
<?php ActiveForm::end(); ?>