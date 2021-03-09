<?php

/**
 * @var artsoft\widgets\ActiveForm $form
 * @var artsoft\models\Role $model
 */

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

?>
<div class="role-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'role-form',
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
                        </div>
                    </div>
                </div>
                    <div class="form-group">
                        <div class="form-group btn-group">
                            <?= \artsoft\helpers\ButtonHelper::submitButtons($model, '/user/role/index', ['delete', 'id' => $model->name]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <?php ActiveForm::end(); ?>
</div>