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
    'id' => 'studyplan-progress',
    'validateOnBlur' => false,
])
?>
<div class="studyplan-progress-search">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $form->field($model_date, "date_in")->widget(DatePicker::class, [
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => ''],
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'MM.yyyy',
                            'autoclose' => true,
                            'minViewMode' => 1,
                            'todayBtn' => 'linked',
                            'todayHighlight' => true,
                        ],
                    'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }" ]
                    ]
            )->label('Месяц и год');
            ?>
            <?= $form->field($model_date, 'subject_id')->dropDownList( \common\models\teachers\TeachersLoadView::getSectListForTeachers($modelTeachers->id, $plan_year),
                [
                    'disabled' => false,
                    'onchange'=>'js: $(this).closest("form").submit()',
                ])->label('Предмет');
            ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>