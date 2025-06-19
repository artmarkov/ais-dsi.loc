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
    'id' => 'stat-search',
    'validateOnBlur' => false,
])
?>
<div class="stat-search">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
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
                                'orientation' => 'bottom',
                            ],
//                        'pluginEvents' => ['changeDate' => "function(e){
//                                           $(e.target).closest('form').submit();
//                                        }"]
                        ]
                    )->label('Дата начала периода'); ?>

                    <?= $form->field($model_date, "date_out")->widget(DatePicker::class, [
                            'type' => \kartik\date\DatePicker::TYPE_INPUT,
                            'options' => ['placeholder' => ''],
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'MM.yyyy',
                                'autoclose' => true,
                                'minViewMode' => 1,
                                'todayBtn' => 'linked',
                                'todayHighlight' => true,
                                'orientation' => 'bottom',
                            ],
//                        'pluginEvents' => ['changeDate' => "function(e){
//                                           $(e.target).closest('form').submit();
//                                        }"]
                        ]
                    )->label('Дата окончания периода');
                    ?>

                    <?= Html::submitButton('<i class="fa fa-html5" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'send']); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

