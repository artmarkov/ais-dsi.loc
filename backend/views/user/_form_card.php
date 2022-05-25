<?php

use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */
/* @var $readonly */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        Сведения о пропуске
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'access_work_flag')->checkbox(['disabled' => $readonly]) ?>

                <?= $form->field($model, 'key_hex')->textInput(['maxlength' => true])->hint('Для получени пропуска необходимо пройти первичный инструктаж по охране труда.') ?>

                <?= $form->field($model, 'timestamp_deny')->widget(DateTimePicker::class, ['disabled' => $readonly]); ?>
            </div>
        </div>
    </div>
</div>

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