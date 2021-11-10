<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\block\models\Block */
/* @var $form artsoft\widgets\ActiveForm; */
?>

<div class="block-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'block-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true, /*'readonly' => true*/]) ?>

                    <?= $form->field($model, 'content')->widget(trntv\aceeditor\AceEditor::class,
                        [
                            'mode' => 'html',
                            'theme' => 'sqlserver', //chrome,clouds,clouds_midnight,cobalt,crimson_editor,dawn,dracula,dreamweaver,eclipse,iplastic
                            //merbivore,merbivore_soft,sqlserver,terminal,tomorrow_night,twilight,xcode
                        ]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model, '/admin/block') ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
