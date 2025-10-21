<?php

use artsoft\helpers\ButtonHelper;
use artsoft\widgets\ActiveForm;

?>

<div class="mosticket-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'mosticket-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Встречная проверка "Мосбилет"
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'file')->fileInput() ?>

                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= ButtonHelper::saveButton('submitAction', 'saveexit', 'Отправить данные формы'); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
