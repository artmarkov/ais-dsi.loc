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
            
                    <?= $form->field($model, 'studyplan_id')->textInput() ?>

                    <?= $form->field($model, 'invoices_id')->textInput() ?>

                    <?= $form->field($model, 'direction_id')->textInput() ?>

                    <?= $form->field($model, 'teachers_id')->textInput() ?>

                    <?= $form->field($model, 'type_id')->textInput() ?>

                    <?= $form->field($model, 'month_time_fact')->textInput() ?>

                    <?= $form->field($model, 'invoices_tabel_flag')->textInput() ?>

                    <?= $form->field($model, 'invoices_date')->textInput() ?>

                    <?= $form->field($model, 'invoices_summ')->textInput() ?>

                    <?= $form->field($model, 'payment_time')->textInput() ?>

                    <?= $form->field($model, 'payment_time_fact')->textInput() ?>

                    <?= $form->field($model, 'invoices_app')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'invoices_rem')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'status')->textInput() ?>
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
