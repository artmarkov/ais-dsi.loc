<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationSpeciality;
use artsoft\helpers\Html;
use common\models\own\Department;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationSpeciality */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="education-speciality-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'education-speciality-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/education/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => Department::getDepartmentList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Department...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                    <?= $form->field($model, 'subject_type_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => \common\models\subject\SubjectType::getTypeList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Subject Type...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Type'));
                    ?>

                    <?= $form->field($model, 'status')->dropDownList(EducationSpeciality::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
