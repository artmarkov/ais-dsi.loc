<?php

use artsoft\widgets\ActiveForm;
use common\models\subject\Subject;
use artsoft\helpers\Html;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectVid;
use common\models\own\Department;

/* @var $this yii\web\View */
/* @var $model common\models\subject\Subject */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-form">

    <?php
    $form = ActiveForm::begin();
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => Department::getDepartmentList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                    <?= $form->field($model, 'category_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => SubjectCategory::getCategoryList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Subject Category...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Category'));
                    ?>
                    <?= $form->field($model, 'vid_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => SubjectVid::getVidList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Subject Vid...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Vid'));
                    ?>
                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(Subject::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
