<?php

use artsoft\widgets\ActiveForm;
use common\models\schoolplan\SchoolplanProtocol;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-protocol-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'schoolplan-protocol-form',
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
            
                    <?= $form->field($model, 'schoolplan_id')->textInput() ?>

                    <?= $form->field($model, 'protocol_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'protocol_date')->textInput() ?>

                    <?= $form->field($model, 'leader_id')->textInput() ?>

                    <?= $form->field($model, 'secretary_id')->textInput() ?>

                    <?= $form->field($model, 'members_list')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'subject_list')->textInput() ?>

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
