<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\studygroups\SubjectSect;
use artsoft\helpers\Html;
use kartik\sortinput\SortableInput;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\studygroups\SubjectSect */
/* @var $form artsoft\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Класс: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Класс: " + (index + 1))
    });
});
';
$this->registerJs($js);
?>

<div class="subject-sect-form">

    <?php
    $form = ActiveForm::begin([
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

                            // 'disabled' => $readonly,
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
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/studygroups/default/subject-cat'])
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectForUnionAndCat($model->union_id, $model->subject_cat_id),
                        'options' => [
                            'prompt' => Yii::t('art', 'Select...'),
                            // 'disabled' => $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id', 'subject_cat_id', 'subject_vid_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/studygroups/default/subject'])
                        ]
                    ]); ?>

                    <?= $form->field($model, 'subject_vid_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('subject_vid_name')->getList(),
                        'options' => [
                            'id' => 'subject_vid_id',
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_type_id')->dropDownList(\common\models\subject\SubjectType::getTypeList()) ?>

                    <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'options' => [
                            // 'disabled' => $model->course ? true : $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Course...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Course'));
                    ?>

                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            // 'disabled' => $model->plan_year ? true : $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>


                </div>
            </div>
            <!--                            --><?php //if(!$model->isNewRecord):?>
            <div class="row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                    <?php
                    echo SortableInput::widget([
                        'name' => 'kv-conn-1',
                        'items' => [
                            1 => ['content' => 'Item # 1'],
                            2 => ['content' => 'Item # 2'],
                            3 => ['content' => 'Item # 3'],
                            4 => ['content' => 'Item # 4'],
                            5 => ['content' => 'Item # 5'],
                        ],
                        'hideInput' => false,
                        'sortableOptions' => [
                            'connected' => true,
                        ],
                        'options' => ['class' => 'form-control', 'readonly' => true]
                    ]);
                    ?>
                </div>
                <div class="col-sm-4">
                    <?php
                    echo SortableInput::widget([
                    'name' => 'kv-conn-2',
                    'items' => [
                    10 => ['content' => 'Item # 10'],
                    20 => ['content' => 'Item # 20'],
                    30 => ['content' => 'Item # 30'],
                    40 => ['content' => 'Item # 40'],
                    50 => ['content' => 'Item # 50'],
                    ],
                    'hideInput' => false,
                    'sortableOptions' => [
                    'itemOptions' => ['class' => 'alert alert-warning'],
                    'connected' => true,
                    ],
                    'options' => ['class' => 'form-control', 'readonly' => true]
                    ]);
                    ?>
<!--                    --><?php //DynamicFormWidget::begin([
//                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
//                        'widgetBody' => '.container-items', // required: css class selector
//                        'widgetItem' => '.item', // required: css class
//                        'limit' => 10, // the maximum times, an element can be added (default 999)
//                        'min' => 1, // 0 or 1 (default 1)
//                        'insertButton' => '.add-item', // css class
//                        'deleteButton' => '.remove-item', // css class
//                        'model' => $modelsDependence[0],
//                        'formId' => 'parents-form',
//                        'formFields' => [
//                            'subject_sect_id',
//                            'studyplan_list',
//                        ],
//                    ]); ?>
<!--                    <div class="panel panel-primary">-->
<!--                        <div class="panel-heading">-->
<!--                            Распределение по классам-->
<!--                        </div>-->
<!--                        <div class="panel-body">-->
<!--                            <div class="container-items"><!-- widgetBody -->-->
<!--                                --><?php //foreach ($modelsDependence as $index => $modelDependence): ?>
<!--                                    <div class="item panel panel-info"><!-- widgetItem -->-->
<!--                                        <div class="panel-heading">-->
<!--                                            <span class="panel-title-activities">Класс: --><?//= ($index + 1) ?><!--</span>-->
<!--                                            --><?php //if (!$readonly): ?>
<!--                                                <div class="pull-right">-->
<!--                                                    <button type="button" class="remove-item btn btn-default btn-xs">-->
<!--                                                        удалить-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                            --><?php //endif; ?>
<!--                                            <div class="clearfix"></div>-->
<!--                                        </div>-->
<!--                                        <div class="panel-body">-->
<!--                                            --><?php
//                                            // necessary for update action.
//                                            if (!$modelDependence->isNewRecord) {
//                                                echo Html::activeHiddenInput($modelDependence, "[{$index}]id");
//                                            }
//                                            ?>
<!--                                            --><?//= $form->field($modelDependence, "[{$index}]relation_id")->dropDownList(\common\models\guidesys\UserRelation::getRelationList(), [
//                                                'prompt' => Yii::t('art/student', 'Select Relations...'),
//                                            ])->label(Yii::t('art/student', 'Relation'));
//                                            ?>
<!---->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                --><?php //endforeach; ?>
<!--                            </div>-->
<!---->
<!--                        </div><!-- .panel -->-->
<!--                        --><?php //if (!$readonly): ?>
<!--                            <div class="panel-footer">-->
<!--                                <div class="form-group btn-group">-->
<!--                                    <button type="button" class="add-item btn btn-success btn-sm pull-right">-->
<!--                                        <i class="glyphicon glyphicon-plus"></i> Добавить-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        --><?php //endif; ?>
<!--                        --><?php //DynamicFormWidget::end(); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--                            --><?php //endif;?>
    <div class="panel-footer">
        <div class="form-group btn-group">
            <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
        </div>
        <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
