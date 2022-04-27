<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanInvoices */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="studyplan-invoices-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'studyplan-invoices-form',
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
                    <?php
                        echo \yii\helpers\Html::activeHiddenInput($model, 'studyplan_id');
                    ?>
                    <?php
                    echo $form->field($model, 'invoices_id')->dropDownList(\common\models\own\Invoices::getInvoicesList(), [
                        'prompt' => Yii::t('art', 'Select...'),
                      //  'disabled' => $readonly,
                    ]);
                    ?>

                    <?= $form->field($model, 'status')->radioList($model->getStatusList()) ?>

                    <?= $form->field($model, 'direction_id')->textInput() ?>

                    <?= $form->field($model, 'teachers_id')->textInput() ?>

                    <?= $form->field($model, 'type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectType::getTypeList(),
                        'options' => [
                           // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Type'));
                    ?>

                    <?= $form->field($model, 'month_time_fact')->textInput() ?>

                    <?= $form->field($model, 'invoices_tabel_flag')->checkbox() ?>

                    <?= $form->field($model, 'invoices_summ')->textInput() ?>

                    <?= $form->field($model, 'invoices_app')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'invoices_rem')->textInput(['maxlength' => true]) ?>

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
