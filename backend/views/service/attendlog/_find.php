<?php

use artsoft\widgets\ActiveForm;
use common\models\service\UsersAttendlog;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = Yii::t('art', 'Finding');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Attendlogs'), 'url' => ['service/attendlog/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="users-attendlog-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'users-attendlog-find',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?=  Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    $options0 = ($model->key_flag == 0) ? ['options' => ['style' => 'display:none']] : [];
                    $options1 = ($model->key_flag == 1) ? ['options' => ['style' => 'display:none']] : [];
                    ?>
                    <?= $form->field($model, 'key_flag')->radioList([0 => 'Поиск по пропуску', 1 => 'Поиск по имени пользователя'])->label('Выбор') ?>

                    <?= $form->field($model, 'key_hex', $options1)->textInput(['maxlength' => true])->label('Пропуск') ?>

                    <?= $form->field($model, 'user_common_id', $options0)->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees', 'students']),
                        'showToggleAll' => false,
                        'options' => [
                          //  'disabled' => true,
                            'value' => $model->user_common_id,
                            'placeholder' => Yii::t('art/guide', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'minimumInputLength' => 3,
                        ],

                    ])->label(Yii::t('art', 'Username'));
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Поиск', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>

            </div>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
    $('input[name="DynamicModel[key_flag]"]').click(function(){
       $(this).val() == 0 ? $('.field-dynamicmodel-key_hex').show() : $('.field-dynamicmodel-key_hex').hide();
       $(this).val() == 1 ? $('.field-dynamicmodel-user_common_id').show() : $('.field-dynamicmodel-user_common_id').hide();
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
