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
    'id' => 'working-time-search-form',
    'validateOnBlur' => false,
])
?>
    <div class="working-time-search-form">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model_date, 'date')->widget(DatePicker::class, [
                            'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }"
                            ]
                        ])->label('Дата');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>