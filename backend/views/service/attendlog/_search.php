<?php

use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'attendlog-search',
    'validateOnBlur' => false,
])
?>
    <div class="attendlog-search">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $form->field($model_date, 'date')->widget(DatePicker::class, [
                    'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }"
                    ]
                ])->label('Дата');
                ?>
                <?php
                if ($model_date->date == Yii::$app->formatter->asDate(time())) {
                    echo \artsoft\helpers\ButtonHelper::createButton(['/service/attendlog/find']);
                }
                ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>