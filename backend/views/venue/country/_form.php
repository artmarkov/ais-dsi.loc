<?php

use artsoft\widgets\ActiveForm;
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

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'fips')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
