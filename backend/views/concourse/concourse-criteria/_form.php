<?php

use artsoft\widgets\ActiveForm;
use common\models\question\QuestionAttribute;
use artsoft\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\question\QuestionAttribute */
/* @var $form artsoft\widgets\ActiveForm */

?>

<div class="concourse-criteria-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => false]
        ],
        'id' => 'concourse-criteria-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка критерия
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // necessary for update action.
                        echo Html::activeHiddenInput($model, 'concourse_id');
                    ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => false]) ?>

                    <?= $form->field($model, 'name_dev')->textInput(['maxlength' => true, 'disabled' => false]) ?>

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
