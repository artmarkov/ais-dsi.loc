<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */
/* @var $form artsoft\widgets\ActiveForm */

?>

    <div class="activities-form">
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'id' => 'activities-form',
    'options' => [
        'data-pjax' => true
    ],
    'action' => !$model->isNewRecord ? ['/activities/default/update-event', 'id' => $model->id] : ['/activities/default/create-event'],
    'enableClientValidation' => true,
]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'category_id')
                        ->dropDownList(\common\models\activities\ActivitiesCat::getCatList(), [
                            'prompt' => Yii::t('art/guide', 'Select Cat...')
                        ])->label(Yii::t('art/guide', 'Name Category'));
                    ?>

                    <?= $form->field($model, 'auditory_id')
                        ->dropDownList(\artsoft\helpers\RefBook::find('auditory_memo_1', 1, true)->getList(), [
                            'prompt' => Yii::t('art/guide', 'Select Auditory...')
                        ])->label(Yii::t('art/guide', 'Name Auditory'));
                    ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<!--                    --><?//= $form->field($model, 'all_day')->checkbox() ?>
<!--                    datetime_in, datetime_out-->
                    <?= $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(); ?>

                    <?= $form->field($model, 'datetime_out')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput() ?>

                </div>

            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::modalButtons('cancel-activities'); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>

    </div>
<?php
$js = <<<JS
$('.cancel-activities').on('click', function (e) {
         e.preventDefault();
         closeModal();
});

function closeModal() {
    $('#activities-modal').modal('hide');
}
JS;

$this->registerJs($js);
?>