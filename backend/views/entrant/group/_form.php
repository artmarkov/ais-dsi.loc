<?php

use artsoft\widgets\ActiveForm;
use common\models\entrant\EntrantGroup;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantGroup */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="entrant-group-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'entrant-group-form',
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
            
                    <?= $form->field($model, 'comm_id')->textInput() ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'prep_flag')->textInput() ?>

                    <?= $form->field($model, 'timestamp_in')->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'created_at')->textInput() ?>

                    <?= $form->field($model, 'updated_at')->textInput() ?>

                    <?= $form->field($model, 'version')->textInput() ?>
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
