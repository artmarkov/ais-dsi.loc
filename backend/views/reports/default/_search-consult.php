<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'teachers-schedule-search',
    'validateOnBlur' => false,
])
?>
    <div class="teachers-schedule-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        if(\artsoft\Art::isBackend()) {
                            echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList(),
                                'options' => [
//                                    'onchange'=>'js: $(this).closest("form").submit()',
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/teachers', 'Teacher'));
                        }
                        ?>
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
//                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>

                        <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
                        <?= Html::submitButton('<i class="fa fa-copy" aria-hidden="true"></i> Создать документ в папке "Документы"', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'doc']); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

