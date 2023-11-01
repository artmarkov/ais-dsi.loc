<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-invoices-search',
    'validateOnBlur' => false,
])
?>
    <div class="studyplan-invoices-search">
    <div class="panel">
        <div class="panel-heading">
            Счета за обучение
        </div>
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
                            'pluginEvents' => ['changeDate' => "function(e){
                                           $(e.target).closest('form').submit();
                                        }"]
                        ]
                    )->label('Месяц и год'); ?>

                </div>
            </div>
            <?php if (\artsoft\Art::isBackend()): ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Параметры фильтрации
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Программа
                                        </div>
                                        <div class="panel-body">
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
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Преподаватель
                                        </div>
                                        <div class="panel-body">
                                            <?= $form->field($model_date, 'direction_id')->widget(\kartik\select2\Select2::class, [
                                                'data' => \common\models\guidejob\Direction::getDirectionList(),
                                                'options' => [
                                                    'id' => 'direction_id',
                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                ],
                                            ])->label(Yii::t('art/teachers', 'Name Direction'));
                                            ?>

                                            <?= $form->field($model_date, 'teachers_id')->widget(\kartik\depdrop\DepDrop::class, [
                                                'data' => \common\models\teachers\Teachers::getTeachersList($model_date->direction_id),
                                                'options' => [
                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                ],
                                                'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'depends' => ['direction_id'],
                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                    'url' => Url::to(['/teachers/default/teachers'])
                                                ]
                                            ])->label(Yii::t('art/teachers', 'Teacher'));
                                            ?>
                                        </div>
                                    </div>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Учебный план
                                        </div>
                                        <div class="panel-body">
                                            <?= $form->field($model_date, 'status')->dropDownList(\common\models\studyplan\Studyplan::getStatusList(), ['prompt' => Yii::t('art', 'Select...')])->label('Статус учебного плана');
                                            ?>
                                            <?= $form->field($model_date, "student_id")->widget(\kartik\select2\Select2::class, [
                                                'data' => \common\models\studyplan\Studyplan::getStudentListForPlanYear($plan_year),
                                                'options' => [
                                                    'multiple' => true,
                                                    'placeholder' => Yii::t('art', 'Select...'),
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true
                                                ],
                                            ])->label(Yii::t('art/student', 'Student')); ?>

                                            <?= $form->field($model_date, "subject_form_id")->dropDownList(RefBook::find('subject_form_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Form')); ?>

                                            <?= $form->field($model_date, 'studyplan_mat_capital_flag')->checkbox()->label('Возможность использования Мат.капиталла.') ?>

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
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            Квитанция
                                        </div>
                                        <div class="panel-body">
                                            <?= $form->field($model_date, "studyplan_invoices_status")->dropDownList(\common\models\studyplan\StudyplanInvoices::getStatusList(), ['prompt' => Yii::t('art', 'Select...')])->label('Статус платежа'); ?>

                                            <?= $form->field($model_date, "limited_status_id")->dropDownList(\common\models\students\Student::getLimitedStatusList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/student', 'Limited status list')); ?>

                                            <?= $form->field($model_date, 'mat_capital_flag')->checkbox()->label('Факт использования Мат.капиталл.') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group btn-group">
                                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
                                    <?= Html::submitButton('Очистить форму', ['class' => 'btn btn-default', 'name' => 'submitAction', 'id' => 'reset']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
document.querySelector('form').addEventListener('submit', (event) => {
    if(event.submitter.id == 'reset') {
        $("#dynamicmodel-education_cat_id").empty();
        $("#dynamicmodel-programm_id").empty();
        $("#dynamicmodel-direction_id").empty();
        $("#dynamicmodel-teachers_id").empty();
        $("#dynamicmodel-student_id").empty();
        $("#dynamicmodel-course").empty();
        $("#dynamicmodel-status").empty();
        $("#dynamicmodel-subject_id").empty();
        $("#dynamicmodel-subject_type_id").empty();
        $("#dynamicmodel-subject_type_sect_id").empty();
        $("#dynamicmodel-subject_vid_id").empty();
        $("#dynamicmodel-studyplan_invoices_status").empty();
        $("#dynamicmodel-limited_status_id").empty();
    // console.log(event.submitter.id);
    }
  });
JS;
$this->registerJs($js);
