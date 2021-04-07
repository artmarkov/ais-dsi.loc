<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeWorks;
use common\models\creative\CreativeCategory;
use artsoft\models\User;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
?>

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
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Поощрения за работу
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">

                                    </div>
                                </div>
                            </div>
                        </div>
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