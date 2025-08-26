<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use yii\helpers\Url;

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

                    <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => false,
                            'onchange' => 'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                        ])->label(Yii::t('art/studyplan', 'Plan Year'));
                    ?>
                    <?= $form->field($model_date, "education_cat_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_cat_short')->getList(),
                        'options' => [
                            'id' => 'education_cat_id',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Education Cat')); ?>

                    <?= $form->field($model_date, 'programm_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\education\EducationProgramm::getProgrammListByName($model_date->education_cat_id),
                        'options' => [
                            'onchange' => 'js: $(this).closest("form").submit()',
                            'multiple' => true,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'depends' => ['education_cat_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/education/default/programm'])
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm')); ?>
                    <?= $form->field($model_date, 'bad_flag')->checkbox([
                        'disabled' => false,
                        'onchange' => 'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                    ])->label('Показать только проблемные планы'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

