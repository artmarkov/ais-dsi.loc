<?php

use artsoft\widgets\ActiveForm;
use common\models\schoolplan\SchoolplanProtocolItems;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocolItems */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-protocol-items-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'schoolplan-protocol-items-form',
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
            
                    <?= $form->field($model, 'schoolplan_protocol_id')->textInput() ?>

                    <?= $form->field($model, 'studyplan_subject_id')->textInput() ?>

                    <?= $form->field($model, 'thematic_items_list')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'lesson_progress_id')->textInput() ?>

                    <?= $form->field($model, 'winner_id')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'resume')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'status_exe')->textInput() ?>

                    <?= $form->field($model, 'status_sign')->textInput() ?>

                    <?= $form->field($model, 'signer_id')->textInput() ?>

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
