<?php

use artsoft\eav\models\EavAttributeType;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\eav\models\EavAttributeType */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="eav-attribute-type-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'eav-attribute-type-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

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

                        <?= $form->field($model, 'store_type')->dropDownList(EavAttributeType::getStoreTypes(), ['prompt' => '']) ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/eav/attribute-type/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/eav/attribute-type/delete', 'id' => $model->id], [
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
