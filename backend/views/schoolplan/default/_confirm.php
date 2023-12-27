<?php

use artsoft\helpers\Html;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_confirm */
/* @var $readonly */

?>

<?php
$form = ActiveForm::begin([
    'id' => 'schoolplan-confirm',
    'validateOnBlur' => false,
])
?>
    <div class="schoolplan-confirm-search">
        <div class="panel panel-default">
            <div class="panel-heading">
                Статус подписи
            </div>
            <div class="panel-body">
                <?php if (\artsoft\Art::isFrontend() && $readonly): ?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info-circle"></i> Для отправки на утверждение выставите все оценки и заполните необходимые поля.',
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
                <?php endif; ?>
                <div class="row">
                    <?php
                    echo Html::activeHiddenInput($model_confirm, 'schoolplan_id');
                    ?>
                    <?php
                    echo $form->field($model_confirm, 'teachers_sign')->widget(\kartik\select2\Select2::class, [
                        'data' => Teachers::getTeachersByIds(User::getUsersByRole('signerSchoolplan')),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint($model_confirm->confirm_status == 1 ? 'Подписано: ' . Yii::$app->formatter->asDatetime($model_confirm->updated_at) : '');
                    ?>
                    <?= $form->field($model_confirm->loadDefaultValues(), 'confirm_status')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\schedule\SubjectScheduleConfirm::getDocStatusList(),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => true,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],
                    ]);
                    ?>
                    <?php if (\artsoft\Art::isBackend()) : ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model_confirm, 'admin_flag')->checkbox()->label('Добавить сообщение секретарю комиссии') ?>
                                <div id="admin_message">
                                    <?= $form->field($model_confirm, 'sign_message')->textInput()->hint('Введите сообщение для секретаря комиссии') ?>
                                </div>
                                <div class="form-group btn-group pull-right">
                                    <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Согласовать', ['class' => 'btn btn-sm btn-success', 'name' => 'submitAction', 'value' => 'approve', 'disabled' => $model_confirm->confirm_status == 1]); ?>
                                    <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить на доработку', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'modif', 'disabled' => $model_confirm->confirm_status != 1]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (\artsoft\Art::isFrontend()): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group btn-group pull-right">
                                    <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Отправить на согласование', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'send_approve', 'disabled' => in_array($model_confirm->confirm_status, [0,3]) ? $readonly : true]); ?>
                                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Внести изменения', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'make_changes', 'disabled' => in_array($model_confirm->confirm_status, [1,2]) ? $readonly : true]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php

$js = <<<JS
     // Показ модуля сообщения
    $('input[type=checkbox][name="SchoolplanProtocolConfirm[admin_flag]"]').prop('checked') ? $('#admin_message').show() : $('#admin_message').hide();
    $('input[type=checkbox][name="SchoolplanProtocolConfirm[admin_flag]"]').click(function() {
       $(this).prop('checked') ? $('#admin_message').show() : $('#admin_message').hide();
     });
  
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
