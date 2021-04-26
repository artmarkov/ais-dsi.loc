<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeWorks;
use common\models\creative\CreativeCategory;
use artsoft\models\User;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;
use wbraganca\dynamicform\DynamicFormWidget;


/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */

$this->registerJs(<<<JS
$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $( "#creative-works-form" ).submit(); // вызываем событие submit на элементе <form>
	  });
JS
, \yii\web\View::POS_END);

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поощрение: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поощрение: " + (index + 1))
    });
});


JS;

$this->registerJs($js);

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
, \yii\web\View::POS_END);
//
//$this->registerJs(<<<JS
//    $(".dynamicform_wrapper").on('afterInsert', function(e, item) {
//        jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
//       if (jQuery('#teachersefficiency-' + index + '-date_in').data('kvDatepicker')) {
//           jQuery('#teachersefficiency-' + index + '-date_in').kvDatepicker('destroy');
//       }
//        jQuery('#teachersefficiency-' + index + '-date_in').kvDatepicker({"format":"dd.mm.yyyy","minViewMode":1,"maxViewMode":2,"autoclose":true,"language":"ru"});
//
//	  });
//});
//JS
//    , \yii\web\View::POS_END);
//?>

<div class="creative-works-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'creative-works-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientScript' => true, // default
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Сведения о работе
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'category_id')->dropDownList(CreativeCategory::getCreativeCategoryList(), ['prompt' => '', 'encodeSpaces' => true, 'disabled' => $readonly]) ?>
                            <?= $form->field($model, 'status')->dropDownList(CreativeWorks::getStatusList(), ['disabled' => $readonly]) ?>

                            <?= $form->field($model, 'name')->textarea(['rows' => 3]) ?>
                            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\own\Department::getDepartmentList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>
                            <?= $form->field($model, 'teachers_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\user\UserCommon::getTeachersList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/creative', 'Select performers...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/creative', 'Аuthors-performers'));
                            ?>

                            <?= $form->field($model, 'published_at')->widget(DatePicker::class, ['disabled' => $readonly])->textInput(['autocomplete' => 'off']); ?>

                            <?php if (!$model->isNewRecord): ?>
                                <?= $form->field($model, 'created_by')->dropDownList(User::getUsersList(), ['disabled' => $readonly]) ?>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php if (!$model->isNewRecord) : ?>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Загруженные материалы
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => $readonly]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 999, // the maximum times, an element can be added (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelsEfficiency[0],
                        'formId' => 'creative-works-form',
                        'formFields' => [
                            'efficiency_id',
                            'teachers_id',
                            'bonus',
                            'date_in',
                        ],
                    ]); ?>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Поощрения за работу
                        </div>
                        <div class="panel-body">
                            <div class="container-items"><!-- widgetBody -->
                                <?php foreach ($modelsEfficiency as $index => $modelEfficiency): ?>
                                    <div class="item panel panel-info"><!-- widgetItem -->
                                        <div class="panel-heading">
                                            <span class="panel-title-activities">Поощрение: <?= ($index + 1) ?></span>
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
                                            if (!$modelEfficiency->isNewRecord) {
                                                echo Html::activeHiddenInput($modelEfficiency, "[{$index}]id");
                                            }
                                            ?>
                                            <?= $form->field($modelEfficiency, "[{$index}]efficiency_id")->widget(\kartik\tree\TreeViewInput::class, [
                                                'options' => [
                                                    'id' => "efficiency_id{$index}",
                                                    ],
                                                'query' => \common\models\efficiency\EfficiencyTree::find()->andWhere(['root' => 3])->addOrderBy('root, lft'),
                                                'dropdownConfig' => [
                                                    'input' => ['placeholder' => 'Выберите показатель эффективности...'],
                                                ],
                                                'fontAwesome' => false,
                                                'multiple' => false,
                                                'rootOptions' => [
                                                    'label' => '',
                                                    'class' => 'text-default'
                                                ],
                                                'childNodeIconOptions' => ['class' => ''],
                                                'defaultParentNodeIcon' => '',
                                                'defaultParentNodeOpenIcon' => '',
                                                'defaultChildNodeIcon' => '',
                                                'childNodeIconOptions' => ['class' => ''],
                                                'parentNodeIconOptions' => ['class' => ''],
                                            ]);
                                            ?>
                                            <?= $form->field($modelEfficiency, "[{$index}]teachers_id")->widget(\kartik\select2\Select2::class, [
                                                'data' => \common\models\teachers\Teachers::getTeachersList(),
                                                'options' => [
                                                    'disabled' => $readonly,
                                                    'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                                                    'multiple' => false,
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true
                                                ],
                                            ])->label(Yii::t('art/teachers', 'Teachers'));
                                            ?>
                                            <?= $form->field($modelEfficiency, "[{$index}]bonus")->textInput(['maxlength' => true, 'readonly' => !Yii::$app->user->isSuperadmin]) ?>
                                            <?= $form->field($modelEfficiency, "[{$index}]date_in")->widget(DatePicker::class, [
                                                //'id' => "{$index}date_in",
                                                'type' => DatePicker::TYPE_INPUT,
                                                'options' => ['placeholder' => ''],
                                                'convertFormat' => true,
                                                'pluginOptions' => [
                                                    'format' => 'dd.MM.yyyy',
                                                    'minViewMode' => 1,
                                                    'maxViewMode' => 2,
                                                    'autoclose' => true,
                                                ]
                                            ])->textInput(['autocomplete' => 'off']); ?>
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
                        <?php endif; ?>

                    </div>
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