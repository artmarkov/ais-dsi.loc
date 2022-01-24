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
<?php $form = ActiveForm::begin([
    'id' => 'sect-schedule-form',
//    'options' => [
//        'data-pjax' => true
//    ],
    'enableAjaxValidation' => true,
//            'validateOnChange' => true,
//            'validateOnSubmit' => true,
    'action' => ['sect/schedule/update-schedule', 'id' => $model->id, 'subject_sect_id' => $subject_sect_id],

]);

?>
    <div class="sect-schedule-form">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        Элемент расписания занятий:
                        <?php echo RefBook::find('sect_name_1')->getValue($model->subjectSectStudyplan->id); ?>
                    </div>
                    <div class="panel-body">
                        <?php if ($model->isSubjectMontly()): ?>
                            <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                        <?php endif; ?>

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
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$('.cancel-schedule').on('click', function (e) {
         e.preventDefault();
         closeModal();
          $.pjax.reload({container: '#subject-sect-schedule-pjax', async: true});
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
                $.pjax.reload({container: '#subject-sect-schedule-pjax', async: true});
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