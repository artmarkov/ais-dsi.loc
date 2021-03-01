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
        $form = ActiveForm::begin([
            'id' => 'venue-place-form',
            'validateOnBlur' => false,
            'enableAjaxValidation' => true,
            'options' => ['enctype' => 'multipart/form-data'],
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

                                <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                                <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'сontact_person')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

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

                                <?= $form->field($model, 'address')->textInput(['maxlength' => true])->hint(\Yii::t('art', 'Click on the map to get the address and coordinates, then click the button to insert the address into the form')) ?>

                                <?= $form->field($model, 'coords')->widget(\common\widgets\YandexGetCoordsWidget::className())->label(false) ?>


                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/venue/default/index'], ['class' => 'btn btn-default']) ?>
                            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                            <?php if (!$model->isNewRecord): ?>
                                <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                    ['/venue/default/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                        <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                    </div>


                </div>
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

