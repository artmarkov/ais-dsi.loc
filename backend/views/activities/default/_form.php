<?php

use artsoft\widgets\ActiveForm;
use common\models\activities\Activities;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="activities-form">

    <?php
    $form = ActiveForm::begin()
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

                            <?= $form->field($model, 'category_id')
                                ->dropDownList(\common\models\activities\ActivitiesCat::getCatList(), [
                                    'prompt' => Yii::t('art/guide', 'Select Cat...')
                                ])->label(Yii::t('art/guide', 'Name Category'));
                            ?>

                            <?= $form->field($model, 'auditory_id')
                                ->dropDownList(\common\models\auditory\Auditory::getAuditoryList(), [
                                    'prompt' => Yii::t('art/guide', 'Select Auditory...')
                                ])->label(Yii::t('art/guide', 'Name Auditory'));
                            ?>

                            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                            <?= $form->field($model, 'all_day')->checkbox() ?>

                            <?= $form->field($model, 'start_time')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(); ?>

                            <?= $form->field($model, 'end_time')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput() ?>


                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
