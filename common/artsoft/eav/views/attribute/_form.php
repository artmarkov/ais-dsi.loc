<?php

use artsoft\helpers\FA;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\eav\models\EavAttribute */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="eav-attribute-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'eav-attribute-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'default_value')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

                </div>

            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <?php if (!$model->isNewRecord): ?>
                            <div class="form-group clearfix">
                                <label class="control-label"
                                       style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>
                                    : </label>
                                <span><?= $model->id ?></span>
                            </div>
                        <?php endif; ?>

                        <?= $form->field($model, 'required')->checkbox() ?>

                        <?= $form->field($model, 'visible')->checkbox() ?>

                        <?= $form->field($model, 'type_id')->dropDownList(artsoft\eav\models\EavAttributeType::getAttributeTypes()) ?>

                        <?= $form->field($model, 'icon')->dropDownList(FA::getIconsList(), [
                            'class' => 'clearfix form-control fa-font-family',
                            'encode' => false,
                        ]) ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/eav/attribute/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/eav/attribute/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-default',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
