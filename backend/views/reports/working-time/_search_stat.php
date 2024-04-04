<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;
use common\models\education\LessonTest;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'working-time-stat-search-form',
    'validateOnBlur' => false,
])
?>
    <div class="working-time-stat-search-form">
        <div class="panel panel-default">
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
                               /* 'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }" ]*/
                            ]
                        )->label('Месяц и год');
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
                <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>