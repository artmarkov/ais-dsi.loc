<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\studyplan\Studyplan;
use artsoft\helpers\Html;

use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsDependence */

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Дисциплина: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Дисциплина: " + (index + 1))
    });
});


JS;

$this->registerJs($js);
?>

<div class="studyplan-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'studyplan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Основные данные
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/studyplan/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, "student_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('students_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/studyplan', 'Select Student...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/student', 'Student'));
                    ?>
                    <?= $form->field($model, "programm_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_programm_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/studyplan', 'Select Education Programm...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm'));
                    ?>

                    <?= $form->field($model, 'course')->textInput() ?>

                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'status')->dropDownList(Studyplan::getStatusList(), ['disabled' => $readonly]) ?>

                </div>
            </div>
            <!--                --><?php //if (!$model->isNewRecord) : ?>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 999, // the maximum times, an element can be added (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsDependence[0],
                'formId' => 'studyplan-form',
                'formFields' => [
                    'subject_cat_id',
                    'subject_id',
                    'subject_type_id',
                    'week_time',
                    'year_time',
                ],
            ]); ?>

            <div class="panel panel-info">
                <div class="panel-heading">
                    Дисциплины
                </div>
                <div class="panel-body">
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsDependence as $index => $modelDependence): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-activities">Дисциплина: <?= ($index + 1) ?></span>
                                    <?php if (!$readonly): ?>
                                        <div class="pull-right">
                                            <button type="button" class="remove-item btn btn-default btn-xs">
                                                удалить
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelDependence->isNewRecord) {
                                        echo Html::activeHiddenInput($modelDependence, "[{$index}]id");
                                    }
                                    ?>

                                    <?= $form->field($modelDependence, "[{$index}]subject_cat_id")->textInput(['maxlength' => true, 'readonly' => $readonly ? $readonly : !Yii::$app->user->isSuperadmin]) ?>
                                    <?= $form->field($modelDependence, "[{$index}]subject_id")->textInput(['maxlength' => true, 'readonly' => $readonly ? $readonly : !Yii::$app->user->isSuperadmin]) ?>
                                    <?= $form->field($modelDependence, "[{$index}]subject_type_id")->textInput(['maxlength' => true, 'readonly' => $readonly ? $readonly : !Yii::$app->user->isSuperadmin]) ?>
                                    <?= $form->field($modelDependence, "[{$index}]week_time")->textInput(['maxlength' => true, 'readonly' => $readonly ? $readonly : !Yii::$app->user->isSuperadmin]) ?>
                                    <?= $form->field($modelDependence, "[{$index}]year_time")->textInput(['maxlength' => true, 'readonly' => $readonly ? $readonly : !Yii::$app->user->isSuperadmin]) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- .panel -->
                <?php if (!$readonly): ?>
                    <div class="panel-footer">
                        <div class="form-group btn-group">

                            <button type="button" class="add-item btn btn-success btn-sm pull-right">
                                <i class="glyphicon glyphicon-plus"></i> Добавить
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php DynamicFormWidget::end(); ?>
                <!--                    --><?php //endif; ?>

            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
