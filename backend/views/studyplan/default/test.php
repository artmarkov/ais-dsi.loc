<?php

use artsoft\widgets\ActiveForm;
use kartik\editable\Editable;
//echo Editable::widget([
//    'model' => $model,
//    'attribute' => 'name',
//    'asPopover' => true,
//    'format' => Editable::FORMAT_LINK,
//    'inputType' => Editable::INPUT_TEXT,
////    'inlineSettings' => [
////        'templateAfter' =>  Editable::INLINE_AFTER_1,
////        'templateBefore' =>  Editable::INLINE_BEFORE_2,
////    ],
//    'options' => ['class'=>'form-control', 'placeholder'=>'Enter name...'],
//    'formOptions' => [
//        'action' => yii\helpers\Url::toRoute(['/auditory/building/view', 'id' => $model->id]),
//    ],
//]);


$editable = Editable::begin([
    'model' => $model,
    'attribute' => 'student_id',
    'asPopover' => true,
    'size' => \kartik\popover\PopoverX::SIZE_MEDIUM,
    'inputType' => Editable::INPUT_DEPDROP,
    'options' => [
        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'options' => ['id'=>'subcat-id-p', 'placeholder' => 'Select subcat...'],
        'select2Options' => [
            'pluginOptions' => [
                'dropdownParent' => '#subcat-id-p-popover', // set this to "#<EDITABLE_ID>-popover" to ensure select2 renders properly within the popover
                'allowClear' => true,
            ]
        ],
        'pluginOptions'=>[
            'depends'=>['cat-id-p'],
            'url' => \yii\helpers\Url::to(['/site/subcat'])
        ]
    ]
]);
$form = $editable->getForm();
// use a hidden input to understand if form is submitted via POST
$editable->beforeInput = \artsoft\helpers\Html::hiddenInput('kv-editable-depdrop', 1) .
    $form->field($model, 'programm_id')->dropDownList(['' => 'Select cat...'])->label(false) . "\n";
$editable->afterInput = $form->field($model, 'speciality_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'options' => ['id'=>'prod-id-p', 'placeholder' => 'Select prod...'],
        'select2Options' => [
            'pluginOptions' => [
                'dropdownParent' => '#prod-id-p-popover', // set this to "#<EDITABLE_ID>-popover" to ensure select2 renders properly within the popover
                'allowClear' => true,
            ]
        ],
        'pluginOptions'=>[
            'depends'=>['cat-id-p', 'subcat-id-p'],
            'url' => \yii\helpers\Url::to(['/site/prod'])
        ]
    ])->label(false) . "\n";
Editable::end();