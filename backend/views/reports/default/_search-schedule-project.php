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
    'id' => 'schedule-project-search-form',
    'validateOnBlur' => false,
])
?>
<div class="schedule-project-search-form">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                [
                    'disabled' => false,
//                    'onchange'=>'js: $(this).closest("form").submit()',
                ])->label(Yii::t('art/studyplan', 'Plan Year'));
            ?>
            <?= $form->field($model_date, 'count_flag')->checkbox()->label('Показать численность (например [12 уч.])'); ?>
            <?= $form->field($model_date, 'name_flag')->checkbox()->label('Показать название группы или ФИО ученика (например Ансамбль Джаз (05 Бюд.) или Иванов Иван Иванович (5/8 Народные 8ПП))'); ?>
            <?= $form->field($model_date, 'subject_flag')->checkbox()->label('Показать предмет (например Народное творчество(Мелк-гр.))'); ?>
            <?= $form->field($model_date, 'programm_name_flag')->checkbox()->label('Показать название программы (например ПП МУЗ)'); ?>
            <?= $form->field($model_date, 'programm_cat_flag')->checkbox()->label('Показать категорию программы (например ПП)'); ?>

            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>