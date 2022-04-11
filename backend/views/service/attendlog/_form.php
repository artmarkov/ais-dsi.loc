<?php

use artsoft\widgets\ActiveForm;
use common\models\service\UsersAttendlog;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="users-attendlog-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'users-attendlog-form',
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

                    <?= $form->field($model->loadDefaultValues(), 'user_common_id')->widget(\kartik\select2\Select2::class, [
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

                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>

                    <?= $form->field($model, 'timestamp_received')->widget(\kartik\datetime\DateTimePicker::class, ['disabled' => false]); ?>

                    <?= $form->field($model, 'timestamp_over')->widget(\kartik\datetime\DateTimePicker::class, ['disabled' => false]); ?>

                </div>
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
