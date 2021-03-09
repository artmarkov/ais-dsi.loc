<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var artsoft\models\AuthItemGroup $model
 * @var artsoft\widgets\ActiveForm $form
 */
?>

<div class="permission-groups-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'permission-groups-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'autofocus' => $model->isNewRecord ? true : false]) ?>

                            <?= $form->field($model, 'code')->textInput(['maxlength' => 64]) ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::exitButton('/user/permission-groups/index');?>
                        <?= \artsoft\helpers\ButtonHelper::saveButton();?>
                        <?= \artsoft\helpers\ButtonHelper::deleteButton($model, ['delete', 'id' => $model->code]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>






