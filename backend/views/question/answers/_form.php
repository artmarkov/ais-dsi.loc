<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

?>

<div class="answers-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'answers-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка ответа
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">


                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
