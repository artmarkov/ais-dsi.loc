<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\sortinput\SortableInput;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSect */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsSubjectSectStudyplan */
/* @var $modelsSubjectSectStudyplan[0] */
/* @var $modelsTeachersLoad */
/* @var $class_index */

$class_index = $model->getClassIndex();

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("' . $class_index . ': " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("' . $class_index . ': " + (index + 1))
    });
});
';
$this->registerJs($js);

$this->registerJs(<<<JS
$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $( "#subject-sect-form" ).submit(); // вызываем событие submit на элементе <form>
	  });
JS
    , \yii\web\View::POS_END);
?>

<div class="subject-sect-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'subject-sect-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            Информация о группе
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'union_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('union_name', $model->isNewRecord ? \common\models\education\EducationUnion::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'union_id',

                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_cat_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectCategoryForUnion($model->union_id),
                        'options' => [
                            'id' => 'subject_cat_id',
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/sect/default/subject-cat'])
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectForUnionAndCat($model->union_id, $model->subject_cat_id),
                        'options' => [
                            'prompt' => Yii::t('art', 'Select...'),
                            'disabled' => $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id', 'subject_cat_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/sect/default/subject'])
                        ]
                    ]); ?>

                    <?= $form->field($model, 'subject_vid_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectVid::getVidListGroup(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => $model->plan_year ? true : $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>

                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    Дополнительные параметры фильтрации
                </div>
                <div class="panel-body">
                    <div class="row">
                       <?= $form->field($model, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\subject\SubjectType::getTypeList(),
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/guide', 'Subject Type'));
                        ?>
                        <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/guide', 'Course'));
                        ?>

                    </div>
                </div>
            </div>
            <?php if (!$model->isNewRecord): ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Распределение по классам
                </div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Не распределенные ученики
                                </div>
                                <div class="panel-body">
                                    <?= SortableInput::widget([
                                        'name' => 'studyplan',
                                        'items' => $model->getStudyplanForUnion($readonly),
                                        'hideInput' => true,
                                        'sortableOptions' => [
                                            'itemOptions' => ['class' => 'alert alert-info'],
                                            'options' => ['style' => 'min-height: 40px'],
                                            'connected' => true,
                                        ],
                                        'options' => ['class' => 'form-control', 'readonly' => true]
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">

                            <?php DynamicFormWidget::begin([
                                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                                'widgetBody' => '.container-items', // required: css class selector
                                'widgetItem' => '.item', // required: css class
                                'limit' => 10, // the maximum times, an element can be added (default 999)
                                'min' => 1, // 0 or 1 (default 1)
                                'insertButton' => '.add-item', // css class
                                'deleteButton' => '.remove-item', // css class
                                'model' => $modelsSubjectSectStudyplan[0],
                                'formId' => 'subject-sect-form',
                                'formFields' => [
                                    'studyplan_subject_list',
                                ],
                            ]); ?>
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    Распределенные ученики
                                </div>
                                <div class="panel-body">
                                    <div class="container-items"><!-- widgetBody -->
                                        <?php foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan): ?>
                                            <div class="item panel panel-default"><!-- widgetItem -->
                                                <div class="panel-heading">
                                                    <span class="panel-title-activities"><?= $class_index ?>: <?= ($index + 1) ?></span>
                                                    <?php if (!$readonly): ?>
                                                        <div class="pull-right">
                                                            <button type="button"
                                                                    class="remove-item btn btn-default btn-xs">
                                                                удалить
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="panel-body">
                                                    <?php
                                                    // necessary for update action.
                                                    if (!$modelSubjectSectStudyplan->isNewRecord) {
                                                        echo Html::activeHiddenInput($modelSubjectSectStudyplan, "[{$index}]id");
                                                    }
                                                    ?>
                                                    <div class="col-sm-12">
                                                        <?= $form->field($modelSubjectSectStudyplan, "[{$index}]class_name")->textInput(['maxlength' => true]) ?>

                                                        <?= $form->field($modelSubjectSectStudyplan, "[{$index}]subject_type_id")->widget(\kartik\select2\Select2::class, [
                                                            'data' => \common\models\subject\SubjectType::getTypeList(),
                                                            'options' => [
                                                                'disabled' => $readonly,
                                                                'placeholder' => Yii::t('art', 'Select...'),
                                                                'multiple' => false,
                                                            ],
                                                            'pluginOptions' => [
                                                                'allowClear' => true
                                                            ],
                                                        ])->label(Yii::t('art/guide', 'Subject Type'));
                                                        ?>
                                                    </div>

                                                    <?php
                                                    $field = $form->field($modelSubjectSectStudyplan, "[{$index}]studyplan_subject_list");
                                                    echo $field->begin();
                                                    ?>
                                                    <div class="col-sm-12">
                                                        <?= SortableInput::widget([
                                                            'model' => $modelSubjectSectStudyplan,
                                                            'attribute' => "[{$index}]studyplan_subject_list",
                                                            'hideInput' => true,
                                                            'sortableOptions' => [
                                                                'itemOptions' => ['class' => 'alert alert-success'],
                                                                'options' => ['style' => 'min-height: 40px'],
                                                                'connected' => true,
                                                            ],
                                                            'options' => ['class' => 'form-control', 'readonly' => true],
                                                            'delimiter' => ',',
                                                            'items' => $modelSubjectSectStudyplan->getSubjectSectStudyplans($readonly),
                                                        ]); ?>
                                                        <p class="help-block help-block-error"></p>
                                                    </div>
                                                    <?= $field->end(); ?>
                                                    <div class="col-sm-12">
                                                        <span class="pull-right text-lg-right text-danger">
                                                            Всего: <?= count($modelSubjectSectStudyplan->getSubjectSectStudyplans()); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                </div><!-- .panel -->
                                <?php if (!$readonly): ?>
                                    <div class="panel-footer">
                                        <div class="form-group btn-group">
                                            <button type="button"
                                                    class="add-item btn btn-success btn-sm pull-right">
                                                <i class="glyphicon glyphicon-plus"></i> Добавить
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php DynamicFormWidget::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php endif; ?>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
