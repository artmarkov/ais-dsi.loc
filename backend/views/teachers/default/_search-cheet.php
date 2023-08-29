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
    'id' => 'cheet-search',
    'validateOnBlur' => false,
])
?>
    <div class="cheet-search">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                if (\artsoft\Art::isBackend()) {
                    echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList(),
                        'options' => [
                            'onchange' => 'js: $(this).closest("form").submit()',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/teachers', 'Teacher'));
                }

                ?>
                <?= $form->field($model_date, "date_in")->widget(DatePicker::class)->label('Дата начала периода'); ?>

                <?= $form->field($model_date, "date_out")->widget(DatePicker::class)->label('Дата окончания периода'); ?>

                <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>

            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>