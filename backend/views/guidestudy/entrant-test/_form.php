<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\GuideEntrantTest */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="guide-entrant-test-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'guide-entrant-test-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?=  Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'division_id')->widget(\kartik\select2\Select2::className(), [
                        'data' => \common\models\own\Division::getDivisionList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Division...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Division'));
                    ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'name_dev')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(\common\models\entrant\GuideEntrantTest::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?=  \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
