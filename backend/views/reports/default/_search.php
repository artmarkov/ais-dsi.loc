<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'teachers-timesheet-search-form',
    'validateOnBlur' => false,
])
?>
<div class="teachers-timesheet-search-form">
    <div class="panel panel-default">
        <div class="panel-body">

            <?= $form->field($model_date, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                'data' => \artsoft\helpers\RefBook::find('subject_type_name')->getList(),
                'options' => [
                    'id' => 'subject_type_id',
                    'placeholder' => Yii::t('art', 'Select...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],

            ])->label('Тип занятий'); ?>

            <?= $form->field($model_date, 'activity_list')->widget(DepDrop::class, [
                'data' =>  \artsoft\helpers\RefBook::find('teachers_activity_memo', \common\models\user\UserCommon::STATUS_ACTIVE)->getList(),
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'options' => [
                    'id' => 'activity_list',
                    'placeholder' => Yii::t('art', 'Select...'),
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'depends' => ['subject_type_id'],
                    'url' => Url::to(['/reports/default/activity-list'])
                ]
            ])->label('Преподаватели по занимаемым должностям');
            ?>

            <?= $form->field($model_date, "update_list_flag")->checkbox()->label('Обновить список преподавателей'); ?>

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
                ]
            )->label('Месяц и год');
            ?>
            <?= $form->field($model_date, "is_avans")->checkbox()->label('Первая половина заработной платы');
            ?>
            <?= $form->field($model_date, "progress_flag")->checkbox()->label('Учесть посещаемость и успеваемость');
            ?>
            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$("select[name='DynamicModel[subject_type_id]']").find(":selected").val() === '1001' ? $('.field-dynamicmodel-progress_flag').show() : $('.field-dynamicmodel-progress_flag').hide();
document.getElementById("subject_type_id").onchange = function () {
 $(this).val() === '1001' ? $('.field-dynamicmodel-progress_flag').show() : $('.field-dynamicmodel-progress_flag').hide();
}
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
