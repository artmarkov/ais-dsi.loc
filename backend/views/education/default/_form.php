<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationProgramm;
use common\models\education\EducationCat;
use common\models\education\EducationSpeciality;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */
/* @var $form artsoft\widgets\ActiveForm */
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
            Сведения об учебной программе
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/education/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'education_cat_id')->dropDownList(RefBook::find('education_cat', $model->isNewRecord ? EducationCat::STATUS_ACTIVE : '')->getList(),
                        ['prompt' => '', 'encodeSpaces' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'speciality_list')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_speciality', $model->isNewRecord ? EducationSpeciality::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Education Specializations...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'period_study')->textInput() ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'status')->dropDownList(EducationProgramm::getStatusList()) ?>


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
