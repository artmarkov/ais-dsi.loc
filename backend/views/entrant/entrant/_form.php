<?php

use artsoft\widgets\ActiveForm;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\Entrant */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="entrant-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'entrant-form',
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
            
                    <?= $form->field($model, 'student_id')->textInput() ?>

                    <?= $form->field($model, 'comm_id')->textInput() ?>

                    <?= $form->field($model, 'group_id')->textInput() ?>

                    <?= $form->field($model, 'last_experience')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'decision_id')->textInput() ?>

                    <?= $form->field($model, 'mid_mark')->textInput() ?>

                    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'unit_reason_id')->textInput() ?>

                    <?= $form->field($model, 'plan_id')->textInput() ?>

                    <?= $form->field($model, 'course')->textInput() ?>

                    <?= $form->field($model, 'type_id')->textInput() ?>

                    <?= $form->field($model, 'status')->textInput() ?>

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
