<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationCat;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationCat */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="education-cat-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'education-cat-form',
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

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'programm_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'programm_short_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'division_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => \common\models\own\Division::getDivisionList(),
                        'options' => [
                            'disabled' => false,
                            'placeholder' => Yii::t('art/guide', 'Select Division...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Division'));
                    ?>
                    <?= $form->field($model, 'status')->dropDownList(EducationCat::getStatusList()) ?>

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
