<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationProgramm;
use common\models\education\EducationCat;
use artsoft\helpers\RefBook;
use wbraganca\dynamicform\DynamicFormWidget;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsEducationProgrammLevel */
/* @var $modelsEducationProgrammLevelSubject */

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
            Карточка образовательной программы
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'education_cat_id')->dropDownList(RefBook::find('education_cat', $model->isNewRecord ? EducationCat::STATUS_ACTIVE : '')->getList(),
                        ['prompt' => '', 'encodeSpaces' => true, 'disabled' => $model->isNewRecord || Yii::$app->user->isSuperadmin ? $readonly : true]) ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Укажите название образовательной программы') ?>

                    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true])->hint('Укажите сокращенное название образовательной программы') ?>

                    <?= $form->field($model, 'term_mastering')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\ArtHelper::getTermList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Укажите срок обучения')
                    ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'status')->dropDownList(EducationProgramm::getStatusList(), [
                        'disabled' => $readonly
                    ]) ?>

                </div>
            </div>
            <?php if (!$model->isNewRecord): ?>
            <?php
                $education_level_list = RefBook::find('education_level')->getList();
                $subject_category_name_list = RefBook::find('subject_category_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList();
                $subject_vid_name_list = RefBook::find('subject_vid_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList();
            ?>
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => $model->term_mastering +1 ?? 10, // the maximum times, an element can be added (default 999)
                    'min' => 0, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsEducationProgrammLevel[0],
                    'formId' => 'education-programm-form',
                    'formFields' => [
                        'level_id',
                        'course',
                        'year_time_total',
                        'cost_month_total',
                        'cost_year_total',
                    ],
                ]); ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Этапы учебного плана
                    </div>
                    <div class="panel-body">
                        <div class="container-items"><!-- widgetBody -->
                            <?php foreach ($modelsEducationProgrammLevel as $index => $modelEducationProgrammLevel): ?>
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
                                        if (!$modelEducationProgrammLevel->isNewRecord) {
                                            echo Html::activeHiddenInput($modelEducationProgrammLevel, "[{$index}]id");
                                        }
                                        ?>
                                        <?php
                                        //                                        if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
                                        <?php
                                        echo $form->field($modelEducationProgrammLevel, "[{$index}]level_id")->widget(\kartik\select2\Select2::class, [
                                            'data' => $education_level_list,
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
                                        <?php
                                        //                                        endif; ?>
                                        <?= $form->field($modelEducationProgrammLevel, "[{$index}]course")->widget(\kartik\select2\Select2::class, [
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
                                                'modelsEducationProgrammLevelSubject' => $modelsEducationProgrammLevelSubject[$index],
                                                'readonly' => $readonly,
                                                'subject_category_name_list' => $subject_category_name_list ,
                                                'subject_vid_name_list' => $subject_vid_name_list ,

                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= $form->field($modelEducationProgrammLevel, "[{$index}]year_time_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>

                                            <?php
                                            //                                            if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
                                            <?= $form->field($modelEducationProgrammLevel, "[{$index}]cost_month_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>
                                            <?= $form->field($modelEducationProgrammLevel, "[{$index}]cost_year_total")->textInput(['maxlength' => true, 'disabled' => false]) ?>
                                            <!--                                            --><?php //else: ?>
<!--                                            --><?php //$form->field($modelEducationProgrammLevel, "[{$index}]cost_year_total")->textInput(['maxlength' => true, 'disabled' => false])->label('Сумма в рублях за учебный год из средств бюджета') ?>
                                            <!--                                            --><?php //endif; ?>
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
                                    <?= Html::submitButton('<i class="fa fa-plus" aria-hidden="true"></i> Скопировать и добавить', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'copy']); ?>
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
