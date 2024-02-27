<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\models\education\LessonTest;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'summary-progress-search-form',
    'validateOnBlur' => false,
])
?>
    <div class="summary-progress-search-form">
        <div class="panel panel-info">
            <div class="panel-heading">
                Параметры фильтрации
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= \yii\bootstrap\Alert::widget([
                            'body' => '<i class="fa fa-info"></i> Заполните поля, помеченные * и нажмите "Получить данные" или "Выгрузить в Excel". <br/> Поля, помеченные * сохраняются в текущей сессии.',
                            'options' => ['class' => 'alert-info'],
                        ]);
                        ?>
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                        <?= $form->field($model_date, "vid_sert")->radioList([LessonTest::MIDDLE_ATTESTATION => 'Промежуточная аттестация', LessonTest::FINISH_ATTESTATION => 'Итоговая аттестация',])->label('Вид аттестации'); ?>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Программа
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
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

                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Учебный план
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model_date, "subject_type_id")->dropDownList(RefBook::find('subject_type_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Type')); ?>

                                <?= $form->field($model_date, "course")->dropDownList(\artsoft\helpers\ArtHelper::getCourseList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/studyplan', 'Course')); ?>

                                <?= $form->field($model_date, "subject_id")->widget(\kartik\select2\Select2::class, [
                                    'data' => \common\models\subject\Subject::getSubjectByCategory(),
                                    'options' => [
                                        'placeholder' => Yii::t('art', 'Select...'),
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label(Yii::t('art/guide', 'Subject Name')); ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
                    <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>