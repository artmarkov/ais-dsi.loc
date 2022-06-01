<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\ButtonHelper;

/* @var $this yii\web\View */
/* @var $modelQuestion */
/* @var $model */
/* @var $readonly */

$options = [];

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
            Карточка формы
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $modelQuestion->name ?>
                </div>
                <div class="panel-body">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> ' . $modelQuestion->description,
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php foreach ($model->getModel()->all() as $id => $item): ?>
                                <?= $model->getForm($form, $item, ['readonly' => $readonly]); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
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

        <?php ActiveForm::end(); ?>

    </div>
