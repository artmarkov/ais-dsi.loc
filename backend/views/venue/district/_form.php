<?php

use artsoft\widgets\ActiveForm;
use common\models\venue\VenueDistrict;
use artsoft\helpers\Html;
use common\models\venue\VenueSity;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueDistrict */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="venue-district-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'venue-district-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sity_id')
                                ->dropDownList(VenueSity::getVenueSityList(), [
                                    'prompt' => Yii::t('art/guide', 'Select Sity...')
                                ])->label(Yii::t('art/guide', 'Name Sity'));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model);?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
