<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationProgramm;
use common\models\education\EducationCat;
use common\models\education\EducationSpeciality;
use artsoft\helpers\RefBook;
use wbraganca\dynamicform\DynamicFormWidget;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsSubject */
/* @var $modelsTime */

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Этап: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Этап: " + (index + 1))
    });
});


JS;

$this->registerJs($js);
?>

<div class="education-programm-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'education-programm-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Сведения об учебном плане
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'education_cat_id')->dropDownList(RefBook::find('education_cat', $model->isNewRecord ? EducationCat::STATUS_ACTIVE : '')->getList(),
                        ['prompt' => '', 'encodeSpaces' => true, 'disabled' => $model->isNewRecord || Yii::$app->user->isSuperadmin ? $readonly : true]) ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Укажите название учебного плана') ?>

                    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true])->hint('Укажите сокращенное название учебного плана') ?>

                    <?= $form->field($model, 'term_mastering')->textInput(['maxlength' => true])->hint('Укажите срок освоения программы. Например: 5 лет или 3/5/7 лет.') ?>

                    <?= $form->field($model, 'speciality_list')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_speciality', $model->isNewRecord ? EducationSpeciality::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $model->isNewRecord || Yii::$app->user->isSuperadmin ? $readonly : true,
                            'placeholder' => Yii::t('art/guide', 'Select Education Specializations...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'status')->dropDownList(EducationProgramm::getStatusList(), [
                        'disabled' => $readonly
                    ]) ?>

                </div>
            </div>
            <?php if (!$model->isNewRecord): ?>
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 10, // the maximum times, an element can be added (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsSubject[0],
                    'formId' => 'education-programm-form',
                    'formFields' => [
                        'level_id',
                        'course',
                    ],
                ]); ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Этапы учебного плана
                    </div>
                    <div class="panel-body">
                        <div class="container-items"><!-- widgetBody -->
                            <?php foreach ($modelsSubject as $index => $modelSubject): ?>
                                <div class="item panel panel-info"><!-- widgetItem -->
                                    <div class="panel-heading">
                                        <span class="panel-title-activities">Этап: <?= ($index + 1) ?></span>
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
                                        if (!$modelSubject->isNewRecord) {
                                            echo Html::activeHiddenInput($modelSubject, "[{$index}]id");
                                        }
                                        ?>
                                        <?php if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
                                            <?= $form->field($modelSubject, "[{$index}]level_id")->widget(\kartik\select2\Select2::class, [
                                                'data' => RefBook::find('education_level')->getList(),
                                                'options' => [
                                                    'disabled' => $readonly,
                                                    'placeholder' => Yii::t('art/guide', 'Select Education Level...'),
                                                    'multiple' => false,
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true
                                                ],
                                            ]);
                                            ?>
                                        <?php endif; ?>
                                        <?= $form->field($modelSubject, "[{$index}]course")->widget(\kartik\select2\Select2::class, [
                                            'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                                            'options' => [
                                                'disabled' => $readonly,
                                                'placeholder' => Yii::t('art/guide', 'Select Course...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label(Yii::t('art/guide', 'Course'));
                                        ?>
                                        <div class="col-sm-12">
                                            <?= $this->render('_form-time', [
                                                'form' => $form,
                                                'index' => $index,
                                                'model' => $model,
                                                'modelsTime' => $modelsTime[$index],
                                                'readonly' => $readonly,
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= $form->field($modelSubject, "[{$index}]year_time_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>

                                            <?php if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
                                                <?= $form->field($modelSubject, "[{$index}]cost_month_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>
                                                <?= $form->field($modelSubject, "[{$index}]cost_year_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>
                                            <?php else: ?>
                                                <?= $form->field($modelSubject, "[{$index}]cost_year_total")->textInput(['maxlength' => true, 'disabled' => false])->label('Сумма в рублях за учебный год из средств бюджета') ?>
                                            <?php endif; ?>
                                        </div>
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
                </div>
            <?php endif; ?>
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
