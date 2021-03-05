<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\calendar\Event */
/* @var $form artsoft\widgets\ActiveForm */

?>

<div class="event-form">
    <?php Pjax::begin(); ?>
    <?php ActiveForm::$autoIdPrefix = 'a';?>
    <?php $form = ActiveForm::begin([
        'id' => 'event-form',
        'options' => [
            'data-pjax' => true
        ],
        'action' => 'refactor-event',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'category_id') ?>

                    <?= $form->field($model, 'auditory_id') ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'start_timestamp')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(); ?>
                    
                    <?= $form->field($model, 'end_timestamp')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput() ?>
                    
<!--                    --><?//= $form->field($model, 'id')->label(false)->textInput() ?>
                    
                </div>

            </div>
            <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('art', 'Cancel'), ['#'], ['class' => 'btn btn-default cancel-event']) ?>
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a(Yii::t('art', 'Delete'), ['#'], ['class' => 'btn btn-danger remove-event']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>

</div>
<?php
$js = <<<JS
//
//$('.create-event').on('click', function (e) {
//
//    e.preventDefault();
//
//    var eventData;
//            eventData = {
//                id : $('#event-id').val(),
//                category_id : $('#event-category_id').val(),
//                resourceId : $('#event-auditory_id').val(),
//                title : $('#event-title').val(),
//                description : $('#event-description').val(),
//                start : $('#event-start_timestamp').val(),
//                end : $('#event-end_timestamp').val()
//            };
//
//    $.ajax({
//        url: '/admin/calendar/event/refactor-event',
//        data: {eventData : eventData},
//        type: 'POST',
//    success: function (res) {
//                $('#w0').fullCalendar('refetchEvents', JSON);
//                closeModal();
//                //console.log(eventData);
//            },
//            error: function () {
//                alert('Error!!!');
//            }
//        });
//});
//
//$('.remove-event').on('click', function (e) {
//
//    e.preventDefault();
//
//    var id = $('#event-id').val();
//
//    $.ajax({
//        url: '/admin/calendar/event/remove-event',
//        data: {id: id},
//        type: 'POST',
//        success: function (res) {
//       
//         $('#w0').fullCalendar('refetchEvents', JSON);
//                closeModal();
//               // console.log(id);
//            },
//            error: function () {
//                alert('Error!!!');
//            }
//    });
//});

$('.cancel-event').on('click', function (e) {
         e.preventDefault();
         closeModal();
});

function closeModal() {
    $('#event-modal').modal('hide');
}
JS;

$this->registerJs($js);
?>