<?php

use artsoft\widgets\ActiveForm;
use common\models\education\EducationLevel;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationLevel */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="education-level-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'education-level-form',
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

                    <?= $form->field($model, 'status')->textInput() ?>

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
