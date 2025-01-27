<?php

use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\concourse\Concourse;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\concourse\Concourse */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = false;


$js = <<<JS
    function toggleConcourse(value) {
      if (value === '2') {
          $('.field-concourse-users_list').show();
      } else {
          $('.field-concourse-users_list').hide();
      }
    }
    toggleConcourse($('input[name="Concourse[vid_id]"]:checked').val());
    $('input[name="Concourse[vid_id]"]').click(function(){
       toggleConcourse($(this).val());
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>

<div class="concourse-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'concourse-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка формы
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput() ?>

                    <?= $form->field($model, 'description')->widget(\dosamigos\tinymce\TinyMce::className(), [
                        'options' => ['rows' => 6],
                        'language' => 'ru',

                    ]); ?>
<!--                    = $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [-->
<!--                        'data' => User::getUsersListByCategory(['teachers', 'employees']),-->
<!--                        'showToggleAll' => false,-->
<!--                        'options' => [-->
<!--                            'disabled' => $readonly,-->
<!--                            //'value' => $model->author_id,-->
<!--                            'placeholder' => Yii::t('art/guide', 'Select Authors...'),-->
<!--                            'multiple' => false,-->
<!--                        ],-->
<!--                        'pluginOptions' => [-->
<!--                            'allowClear' => false,-->
<!--                            //'minimumInputLength' => 3,-->
<!--                        ],-->
<!---->
<!--                    ])->hint('Укажите автора формы.');-->

                    <?= $form->field($model->loadDefaultValues(), 'vid_id')->radioList(Concourse::getVidList()) ?>
                    <?= $form->field($model, 'authors_ban_flag')->checkbox() ?>

                    <?= $form->field($model, 'users_list')->widget(Select2::className(), [
                        'data' => User::getUsersListByCategory(['teachers']),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Укажите участников.');
                    ?>

                    <?= $form->field($model, 'timestamp_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'timestamp_out')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'status')->dropDownList(Concourse::getStatusList(), ['disabled' => $readonly]) ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Загруженные материалы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?=  \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
