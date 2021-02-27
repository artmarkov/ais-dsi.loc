<?php

use common\widgets\ActiveForm;
use common\models\venue\VenueCountry;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueCountry */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="venue-country-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'venue-country-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'fips')->textInput(['maxlength' => true]) ?>

                </div>

            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
<!--                   // <div class="record-info">-->
                        <div class="form-group clearfix">
                            <label class="control-label"
                                   style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>
                                : </label>
                            <span><?= $model->id ?></span>
                        </div>
                    
                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/venue/country/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/venue-country/default/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-default',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                <?= Html::a(Yii::t('art', 'Add New'), ['/venue/country/create'],
                                    ['class' => 'btn btn-primary pull-right'])
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
<!--                </div>-->
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
