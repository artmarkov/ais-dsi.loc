<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use artsoft\helpers\ButtonHelper;

//print_r($modelAttributes);
//die();
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
                    <?php
                    foreach ($modelAttributes as $id => $item):
                    ?>
                      <?= $form->field($model, $item['name'])
                        ->textInput(['maxlength' => true])
                        ->label($item['label'])
                        ->hint($item['hint'])

                        ?>

                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?php $result = ButtonHelper::exitButton();
                $result .= ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit');
                $result .= ButtonHelper::saveButton();
                echo $result;
                ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
