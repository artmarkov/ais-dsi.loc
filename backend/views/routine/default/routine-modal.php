<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\calendar\Conference */
/* @var $form artsoft\widgets\ActiveForm */

?>

<div class="event-form">

    <?php
    $form = ActiveForm::begin();
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(); ?>
                    
                    <?= $form->field($model, 'end_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(); ?>

                </div>

            </div>
            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::a(Yii::t('art', 'Create'), ['#'],['class' => 'btn btn-primary create-event']) ?>
                    <?= Html::a(Yii::t('art', 'Cancel'), ['#'], ['class' => 'btn btn-default cancel-event']) ?>
                <?php else: ?>
                    
                    <?= Html::a(Yii::t('art', 'Save'), ['#'],['class' => 'btn btn-primary create-event']) ?>
                    <?= Html::a(Yii::t('art', 'Delete'), ['#'], ['class' => 'btn btn-default remove-event']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
//$js = <<<JS
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
//$('.cancel-event').on('click', function (e) {
//         e.preventDefault();
//         closeModal();
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
//
//function closeModal() {
//    $('#event-modal').modal('hide');
//}
//JS;
//
//$this->registerJs($js);
//?>