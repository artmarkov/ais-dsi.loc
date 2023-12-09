<?php

use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'execution-search',
    'validateOnBlur' => false,
])
?>
    <div class="execution-search">
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
                                ],
                                'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }"]
                            ]
                        )->label('Месяц и год');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

