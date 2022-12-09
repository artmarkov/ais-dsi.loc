<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\EducationUnion;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\guidestudy\EducationUnion */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="education-union-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'education-union-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'union_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'programm_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => RefBook::find('education_programm_short_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            Yii::t('art/studyplan', 'Select Education Programm...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm'));
                    ?>
                    <?= $form->field($model, 'class_index')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'maxlength' => true]) ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(EducationUnion::getStatusList()) ?>

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
