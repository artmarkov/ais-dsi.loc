<?php

use artsoft\helpers\Html;
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
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'answers-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
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
<!--                            --><?//= $form->field($model, 'users_id')->label(false)->hiddenInput(['value' => $model->users_id]) ?>
                            <?= $form->field($model, 'users_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                                'showToggleAll' => false,
                                'options' => [
                                    'disabled' => $readonly,
                                    'value' => $model->users_id,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                ],

                            ]);

                            ?>
<!--                            --><?//= $form->field($model, 'users_id')->textInput() ?>

                            <?php foreach ($model->getModel()->all() as $id => $item): ?>
<!--                        --><?php //echo '<pre>' . print_r($item, true) . '</pre>'; die(); ?>
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
