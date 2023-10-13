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

                        <?php if (\artsoft\Art::isBackend()): ?>
                            <?= $form->field($model_date, "programm_id")->widget(\kartik\select2\Select2::class, [
                                'data' => RefBook::find('education_programm_short_name')->getList(),
                                'options' => [
                                    'onchange' => 'js: $(this).closest("form").submit()',
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/studyplan', 'Education Programm')); ?>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Дополнительные параметры фильтрации
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php if (\artsoft\Art::isBackend()): ?>

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

                                        <?php endif; ?>

                                        <?= $form->field($model_date, "student_id")->widget(\kartik\select2\Select2::class, [
                                            'data' => RefBook::find('students_fio')->getList(),
                                            'options' => [
                                                'placeholder' => Yii::t('art', 'Select...'),
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label(Yii::t('art/student', 'Student')); ?>

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

                                        <?= $form->field($model_date, "subject_type_id")->dropDownList(RefBook::find('subject_type_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Type')); ?>

                                        <?= $form->field($model_date, "subject_type_sect_id")->dropDownList(RefBook::find('subject_type_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Type Sect')); ?>

                                        <?= $form->field($model_date, "subject_vid_id")->dropDownList(RefBook::find('subject_vid_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Vid')); ?>

                                        <?= $form->field($model_date, "subject_form_id")->dropDownList(RefBook::find('subject_form_name')->getList(), ['prompt' => Yii::t('art', 'Select...')])->label(Yii::t('art/guide', 'Subject Form')); ?>

                                        <?= $form->field($model_date, "studyplan_invoices_status")->dropDownList(\common\models\studyplan\StudyplanInvoices::getStatusList(), ['prompt' => Yii::t('art', 'Select...')])->label('Статус платежа'); ?>

                                        <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
                                        <?= Html::submitButton('Очистить форму', ['class' => 'btn btn-default', 'name' => 'submitAction', 'id' => 'reset']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
document.querySelector('form').addEventListener('submit', (event) => {
    if(event.submitter.id == 'reset') {
        $("#dynamicmodel-direction_id").empty();
        $("#dynamicmodel-teachers_id").empty();
        $("#dynamicmodel-student_id").empty();
        $("#dynamicmodel-course").empty();
        $("#dynamicmodel-subject_id").empty();
        $("#dynamicmodel-subject_type_id").empty();
        $("#dynamicmodel-subject_type_sect_id").empty();
        $("#dynamicmodel-subject_vid_id").empty();
        $("#dynamicmodel-studyplan_invoices_status").empty();
        
    // console.log(event.submitter.id);
    }
  });
JS;
$this->registerJs($js);
