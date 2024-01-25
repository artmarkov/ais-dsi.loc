<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'statement-search',
    'validateOnBlur' => false,
])
?>
    <div class="statement-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
//                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                        <?= $form->field($model_date, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\RefBook::find('subject_type_name')->getList(),
                            'options' => [
                                'id' => 'subject_type_id',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],

                        ])->label('Форма деятельности'); ?>

                        <?= $form->field($model_date, "is_week_load")->checkbox()->label('Недельная нагрузка');?>
                        <?= $form->field($model_date, "is_consult")->checkbox()->label('Учесть консультации');?>
                        <hr>
                        <?= $form->field($model_date, "print_summ")->checkbox()->label('Вывести на печать сумму часов');?>
                        <?= $form->field($model_date, "print_stat")->checkbox()->label('Вывести на печать статистику');?>
                        <?= $form->field($model_date, "del_free")->checkbox()->label('Убрать пустые строки');?>

                        <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

