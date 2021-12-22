<?php

use artsoft\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\widgets\MaskedInput;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSectSchedule */
/* @var $modelStudyplan common\models\studyplan\Studyplan */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $studyplan_id */

?>

    <div class="schedule-form">
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'id' => 'schedule-form',
    'options' => [
        'data-pjax' => true
    ],
   //'action' => false,
//    'action' => !$model->isNewRecord ? ['sect/schedule/update-schedule', 'id' => $model->id, 'studyplan_id' => $studyplan_id] : ['sect/schedule/create-schedule', 'studyplan_id' => $studyplan_id],
    'enableAjaxValidation' => true,
]);

?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
<!--                    --><?//= $form->field($model, "teachersLoadId")->dropDownList($modelStudyplan->getStudyplanTeachersLoad(), ['options' => [$model->getTeachersLoadId() => ['Selected' => true]]]); ?>
                    <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                    <?= $form->field($model, "time_in")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time in...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_out")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time out...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>
                    <?= $form->field($model, "description")->textarea(['placeholder' => Yii::t('art/guide', 'Enter description...')]) ?>

                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::modalButtons('cancel-schedule', 'delete-schedule'); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>

    </div>
<?php
$js = <<<JS
$('.cancel-schedule').on('click', function (e) {
         e.preventDefault();
         closeModal();
          $.pjax.reload({container: '#studyplan-grid-pjax', async: true});
});
$('.delete-schedule').on('click', function (e) {
        // e.preventDefault();
         var id = $model->id;

    $.ajax({
        url: '/admin/sect/schedule/delete-schedule',
        data: {id: id},
        type: 'POST',
        success: function (res) {
            
                closeModal();
                $.pjax.reload({container: '#studyplan-grid-pjax', async: true});
               // console.log(id);
            },
            error: function () {
                alert('Error!!!');
            }
    });
        
});
function closeModal() {
    $('#schedule-modal').modal('hide');
}
JS;

$this->registerJs($js);
?>