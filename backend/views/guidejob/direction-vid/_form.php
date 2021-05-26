<?php

use artsoft\widgets\ActiveForm;
use common\models\guidejob\Direction;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\guidejob\Direction */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="direction-vid-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'directionn-form',
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

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
