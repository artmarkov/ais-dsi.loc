<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\auditory\Auditory;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'schedule-search',
    'validateOnBlur' => false,
]);
$auditoryModels = (new \yii\db\Query())->from('auditory_view')->where(['=', 'study_flag', true])->andWhere(['cat_id' => [1000,1001,1002]])->all();
$modelsAuditory = \yii\helpers\ArrayHelper::map($auditoryModels,'id','auditory_memo_1');
?>
    <div class="schedule-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                            echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList(),
                                'options' => [
//                                    'multiple' => true,
//                                    'onchange'=>'js: $(this).closest("form").submit()',
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/teachers', 'Teacher'));
                        ?>
                        <?php
                        echo $form->field($model_date, 'auditory_id')->widget(\kartik\select2\Select2::class, [
                            'data' => $modelsAuditory,
                            'options' => [
//                                    'multiple' => true,
//                                'onchange'=>'js: $(this).closest("form").submit()',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Аудитория');
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

                        <?= $form->field($model_date, 'course')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                            'options' => [
                                'multiple' => false,
                                'placeholder' => Yii::t('art', 'Select...'),
//                                'onchange'=>'js: $(this).closest("form").submit()',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/studyplan', 'Course'));
                        ?>
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
//                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
                    <?= Html::submitButton('Очистить форму', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'reset']) ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

