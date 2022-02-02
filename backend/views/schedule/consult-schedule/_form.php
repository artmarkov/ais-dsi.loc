<?php

use artsoft\widgets\ActiveForm;
use common\models\schedule\ConsultSchedule;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\ConsultSchedule */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="consult-schedule-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'consult-schedule-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?=  Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                    
                    <?= $form->field($model, 'teachers_load_id')->textInput() ?>

                    <?= $form->field($model, 'datetime_in')->textInput() ?>

                    <?= $form->field($model, 'datetime_out')->textInput() ?>

                    <?= $form->field($model, 'auditory_id')->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'created_at')->textInput() ?>

                    <?= $form->field($model, 'created_by')->textInput() ?>

                    <?= $form->field($model, 'updated_at')->textInput() ?>

                    <?= $form->field($model, 'updated_by')->textInput() ?>

                    <?= $form->field($model, 'version')->textInput() ?>

                        </div>
                    </div>
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
