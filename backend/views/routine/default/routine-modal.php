<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\routine\Routine */
/* @var $form artsoft\widgets\ActiveForm */

?>
    <div class="routine-form">
        <?php Pjax::begin(); ?>
        <?php $form = ActiveForm::begin([
            'id' => 'routine-form',
            'options' => [
                'data-pjax' => true
            ],
            'action' => 'create-event',
            'enableClientValidation' => true,
        ]) ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <?= $form->field($model, 'cat_id')
                            ->dropDownList(\common\models\routine\RoutineCat::getCatList(), [
                                'prompt' => Yii::t('art/guide', 'Select Cat...')
                            ])->label(Yii::t('art/guide', 'Category'));
                        ?>

                        <?= $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(); ?>

                        <?= $form->field($model, 'end_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>

                        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'users_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\models\User::getUsersListByCategory(['teachers']),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => false,
                                'value' => $model->users_id,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],

                        ]);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::closeButton('cancel-event');?>
                        <?= \artsoft\models\User::hasPermission('editRoutine') ? \artsoft\helpers\ButtonHelper::saveButton() : null;?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>
    </div>
<?php
$js = <<<JS

$('.cancel-event').on('click', function (e) {
         e.preventDefault();
         closeModal();
});

function closeModal() {
    $('#routine-modal').modal('hide');
}
JS;
$this->registerJs($js);
?>

<?php
$js = <<<JS
$("select[name='Routine[cat_id]']").find(":selected").val() === '1005' ? $('.field-routine-users_id').show() : $('.field-routine-users_id').hide();
document.getElementById("routine-cat_id").onchange = function () {
 $(this).val() === '1005' ? $('.field-routine-users_id').show() : $('.field-routine-users_id').hide();
}
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
