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
                        'onchange' => 'js: $(this).closest("form").submit()',
                    ])->label(Yii::t('art/studyplan', 'Plan Year'));
                ?>
                <?= $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \artsoft\helpers\RefBook::find('teachers_fio', /*$model_date->plan_year == \artsoft\helpers\ArtHelper::getStudyYearDefault() ? 1 : */ 0)->getList(),
                    'options' => [
//                                    'onchange'=>'js: $(this).closest("form").submit()',
                        'placeholder' => Yii::t('art', 'Select...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(Yii::t('art/teachers', 'Teacher'));
                ?>
                <?= $form->field($model_date, "is_history")->checkbox()->label('Учесть все планы учеников(закрытые, отчисленные, переведенные)'); ?>

            </div>
            <div class="panel-footer">
                <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-success', 'name' => 'submitAction', 'value' => 'excel']); ?>
                <?= Html::submitButton('<i class="fa fa-copy" aria-hidden="true"></i> Скопировать в папку "Документы"', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'doc']); ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>