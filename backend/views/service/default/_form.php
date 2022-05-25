<?php

use artsoft\widgets\ActiveForm;
use artsoft\auth\assets\AvatarAsset;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */
/* @var $form artsoft\widgets\ActiveForm */

AvatarAsset::register($this);
?>

    <div class="users-card-form">

        <?php
        $form = ActiveForm::begin([
            'id' => 'users-card-form',
            'validateOnBlur' => false,
        ])
        ?>

        <div class="panel">
            <div class="panel-heading">
                Карточка пропуска
                <?php if (!$model->isNewRecord):?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-10">

                        <?= $form->field($model->loadDefaultValues(), 'user_common_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees', 'students', 'parents']),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => true,
                                'value' => $model->user_common_id,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                'minimumInputLength' => 3,
                            ],

                        ])->label(Yii::t('art', 'Username'));

                        ?>
                        <?= $form->field($model, 'access_work_flag')->checkbox(['disabled' => false]) ?>

                        <?= $form->field($model, 'key_hex')->textInput(['maxlength' => true])->hint('Для получени пропуска необходимо пройти первичный инструктаж по охране труда.') ?>

                        <?= $form->field($model, 'timestamp_deny')->widget(\kartik\datetime\DateTimePicker::class, ['disabled' => false]); ?>


                    </div>
                    <div class="col-sm-2">
                        <div class="image-preview"">
                            <img src="<?= $model->getSigurPhoto() ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$css = <<<CSS
.image-preview img {
    width: 150px;
    height: 150px;
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 1px;
}

CSS;

$this->registerCss($css);
?>

<?php
$js = <<<JS
    function toggle(field) {
       if ($(field).is(':checked') ) {
        $('input[name="UsersCard[key_hex]"]').attr("disabled", false);
        $('input[name="UsersCard[timestamp_deny]"]').attr("disabled", false);
    } else {
        $('input[name="UsersCard[key_hex]"]').attr("disabled", true);
        $('input[name="UsersCard[timestamp_deny]"]').attr("disabled", true);
    }
    }
    toggle('input[name="UsersCard[access_work_flag]"]');
    $('input[name="UsersCard[access_work_flag]"]').on('click', function () {
        console.log(this);
       toggle(this);
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>