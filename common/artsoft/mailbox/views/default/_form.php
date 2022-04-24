<?php

use artsoft\widgets\ActiveForm;
use artsoft\models\User;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\mailbox\models\Mailbox */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="mailbox-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'mailbox-form',
        'validateOnBlur' => false,
        'enableClientScript' => true, // default
    ])
    ?>
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <?php
                $field = $form->field($model, 'receivers_ids');
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \nex\chosen\Chosen::widget([
                        'model' => $model,
                        'attribute' => 'receivers_ids',
                        'items' => User::getUsersList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/mailbox', 'To:'),
                    ]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>

                <?php
                $field = $form->field($model, 'title');
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \yii\helpers\Html::activeTextInput($model, 'title', ['class' => 'form-control', 'placeholder' => Yii::t('art/mailbox', 'Topic:')]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>

                <?php
                $field = $form->field($model, 'content');
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= dosamigos\tinymce\TinyMce::widget([
                        'model' => $model,
                        'attribute' => 'content',
                        'options' => ['rows' => 3],
                        'language' => 'ru'
                    ]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>


            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true]]) ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <div class="pull-right">

                    <?= Html::submitButton(Yii::t('art/mailbox', 'Draft'), ['class' => 'btn btn-default', 'name' => 'status_post', 'value' => $model::STATUS_POST_DRAFT, 'data-pjax' => 0]) ?>
                    <?= Html::submitButton(Yii::t('art/mailbox', 'Send'), ['class' => 'btn btn-primary', 'name' => 'status_post', 'value' => $model::STATUS_POST_SENT, 'data-pjax' => 0]) ?>

                </div>
                <?= Html::a(Yii::t('art/mailbox', 'Discard'), ['/mailbox/default/index'], ['class' => 'btn btn-default']) ?>
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a(Yii::t('art/mailbox', 'Move to Trash'), ['/mailbox/default/trash-sent', 'id' => $model->id], [
                        'class' => 'btn btn-default',
                        'data-pjax' => 0,
                        'data' => [
                            'confirm' => Yii::t('art/mailbox', 'Are you sure you want to trash this mail?'),
                            'method' => 'post',
                        ],
                    ])
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
