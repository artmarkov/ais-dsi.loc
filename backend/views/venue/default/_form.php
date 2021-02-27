<?php

use common\widgets\ActiveForm;
use common\models\venue\VenuePlace;
use artsoft\helpers\Html;
use yii\widgets\MaskedInput;
use common\models\venue\VenueCountry;
use common\models\venue\VenueDistrict;
use common\models\venue\VenueSity;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenuePlace */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="venue-place-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'venue-place-form',
        'validateOnBlur' => false,
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data'],
    ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'сontact_person')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                          <div class="help-block"><?= \Yii::t('art', 'Click on the map to get the address and coordinates, then click the button to insert the address into the form'); ?></div>
                       
                        </div>
                    </div>

                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'coords')->widget(\common\widgets\YandexGetCoordsWidget::className())->label(false) ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group clearfix">
                        <label class="control-label"
                               style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>
                            : </label>
                        <span><?= $model->id ?></span>
                    </div>

                    <?php
                    echo $form->field($model, 'country_id')->dropDownList(VenueCountry::getVenueCountryList(), [
                        'prompt' => Yii::t('art/guide', 'Select Country...'),
                        'id' => 'country_id'
                    ])->label(Yii::t('art/guide', 'Name Country'));
                    echo $form->field($model, 'sity_id')->widget(DepDrop::classname(), [
                        'data' => VenueSity::getSityByName($model->country_id),
                        'options' => ['prompt' => Yii::t('art/guide', 'Select Sity...'), 'id' => 'sity_id'],
                        'pluginOptions' => [
                            'depends' => ['country_id'],
                            'placeholder' => Yii::t('art/guide', 'Select Sity...'),
                            'url' => Url::to(['/venue/default/sity'])
                        ]
                    ])->label(Yii::t('art/guide', 'Name Sity'));

                    echo $form->field($model, 'district_id')->widget(DepDrop::classname(), [
                        'data' => VenueDistrict::getDistrictByName($model->sity_id),
                        'options' => ['prompt' => Yii::t('art/guide', 'Select District...')],
                        'pluginOptions' => [
                            'depends' => ['sity_id'],
                            'placeholder' => Yii::t('art/guide', 'Select District...'),
                            'url' => Url::to(['/venue/default/district'])
                        ]
                    ])->label(Yii::t('art/guide', 'Name District'));
                    ?>

                    <div class="form-group">
                        <?php if ($model->isNewRecord): ?>
                            <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                            <?= Html::a(Yii::t('art', 'Cancel'), ['/venue/default'], ['class' => 'btn btn-default']) ?>
                        <?php else: ?>
                            <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                            <?= Html::a(Yii::t('art', 'Delete'),
                                ['/venue/default/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-default',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?= Html::a(Yii::t('art', 'Add New'), ['/venue/default/create'],
                                ['class' => 'btn btn-primary pull-right'])
                            ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <?php ActiveForm::end(); ?>

            <?php if (!$model->isNewRecord): ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="record-info">

                            <div class="form-group clearfix">
                                <label class="control-label" style="float: left; padding-right: 5px;">
                                    <?= $model->attributeLabels()['created_at'] ?> :
                                </label>
                                <span><?= $model->createdDatetime; ?></span>
                                <span><?= $model->createdBy->username ?></span>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label" style="float: left; padding-right: 5px;">
                                    <?= $model->attributeLabels()['updated_at'] ?> :
                                </label>
                                <span><?= $model->updatedDatetime; ?></span>
                                <span><?= $model->updatedBy->username ?></span>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$js = <<<JS
$('.insert-coords-form').on('click', function (e) {
    e.preventDefault();   
    document.getElementById('venueplace-address').value = $('#venueplace-map_address').val();       
 });
JS;
$this->registerJs($js);

