<?php

use artsoft\widgets\ActiveForm;

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
                        <?php
                        if(\artsoft\Art::isBackend()) {
                            echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList(),
                                'options' => [
                                    'onchange'=>'js: $(this).closest("form").submit()',
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
                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

