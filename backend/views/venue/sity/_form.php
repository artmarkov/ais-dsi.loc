<?php

use artsoft\widgets\ActiveForm;
use common\models\venue\VenueSity;
use artsoft\helpers\Html;
use common\models\venue\VenueCountry;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueSity */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="venue-sity-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'venue-sity-form',
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

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'latitude')->textInput() ?>

                    <?= $form->field($model, 'longitude')->textInput() ?>

                    <?= $form->field($model, 'country_id')
                        ->dropDownList(VenueCountry::getVenueCountryList(), [
                            'prompt' => Yii::t('art/guide', 'Select Country...')
                        ])->label(Yii::t('art/guide', 'Name Country'));
                    ?>
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
