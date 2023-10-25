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

            <?= $form->field($model_date, "date_in")->widget(DatePicker::class)->label('Дата начала периода'); ?>

            <?= $form->field($model_date, "date_out")->widget(DatePicker::class)->label('Дата окончания периода'); ?>

            <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>