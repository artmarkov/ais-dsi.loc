<?php

use artsoft\widgets\ActiveForm;
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
        $form = ActiveForm::begin()
        ?>

        <div class="panel">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                        <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'сontact_person')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

                        <?= $form->field($model, 'country_id')->widget(\kartik\select2\Select2::class, [
                            'data' => VenueCountry::getVenueCountryList(),
                            'options' => [
                                'id' => 'country_id',
                                //'disabled' => $readonly,
                                'placeholder' => Yii::t('art/guide', 'Select Country...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/guide', 'Name Country'));
                        ?>
                        <?= $form->field($model, 'sity_id')->widget(DepDrop::class, [
                            'data' => VenueSity::getSityByName($model->country_id),
                            'options' => ['prompt' => Yii::t('art/guide', 'Select Sity...'), 'id' => 'sity_id'],
                            'pluginOptions' => [
                                'depends' => ['country_id'],
                                'placeholder' => Yii::t('art/guide', 'Select Sity...'),
                                'url' => Url::to(['/venue/default/sity'])
                            ]
                        ])->label(Yii::t('art/guide', 'Name Sity'));
                        ?>
                        <?= $form->field($model, 'district_id')->widget(DepDrop::class, [
                        'data' => VenueDistrict::getDistrictByName($model->sity_id),
                        'options' => ['prompt' => Yii::t('art/guide', 'Select District...')],
                        'pluginOptions' => [
                        'depends' => ['sity_id'],
                        'placeholder' => Yii::t('art/guide', 'Select District...'),
                        'url' => Url::to(['/venue/default/district'])
                        ]
                        ])->label(Yii::t('art/guide', 'Name District'));
                        ?>
                        <?= $form->field($model, 'address')->textInput(['maxlength' => true])->hint(\Yii::t('art', 'Click on the map to get the address and coordinates, then click the button to insert the address into the form')) ?>
                        <?= $form->field($model, 'coords')->widget(\common\widgets\YandexGetCoordsWidget::className(), ['apikey' => 'cc75ee6b-40b0-4f0e-9814-489e78b633aa'])->label(false) ?>
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

<?php
$js = <<<JS
$('.insert-coords-form').on('click', function (e) {
    e.preventDefault();   
    document.getElementById('venueplace-address').value = $('#venueplace-map_address').val();       
 });
JS;
$this->registerJs($js);

