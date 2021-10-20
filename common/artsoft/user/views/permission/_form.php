<?php

/**
 * @var artsoft\widgets\ActiveForm $form
 * @var artsoft\models\Permission $model
 */

use artsoft\helpers\Html;
use artsoft\models\AuthItemGroup;
use yii\helpers\ArrayHelper;
use artsoft\widgets\ActiveForm;

?>

<div class="permission-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'permission-form',
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
                            <?= $form->field($model, 'description')->textInput(['maxlength' => 255, 'autofocus' => $model->isNewRecord ? true : false]) ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

                            <?= $form->field($model, 'group_code')->dropDownList(ArrayHelper::map(AuthItemGroup::find()->asArray()->all(), 'code', 'name'), ['prompt' => '']) ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::exitButton(['/user/permission/index']);?>
                        <?= \artsoft\helpers\ButtonHelper::saveButton();?>
                        <?= \artsoft\helpers\ButtonHelper::deleteButton(['delete', 'id' => $model->name]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
