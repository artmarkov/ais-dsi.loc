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
    'id' => 'studyplan-timesheet-search',
    'validateOnBlur' => false,
])
?>
<div class="studyplan-stat-search">
    <div class="panel panel-info">
        <div class="panel-heading">
            Параметры фильтрации
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> Заполните поля, помеченные * и нажмите "Выгрузить в Excel".',
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
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
                        ]
                    )->label('Месяц и год');
                    ?>
                </div>
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

                    <?= $form->field($model_date, 'subject_form_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('subject_form_name')->getList(),
                        'options' => [
                            'disabled' => false,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Form'));
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

