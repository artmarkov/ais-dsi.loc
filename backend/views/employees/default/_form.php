<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\employees\Employees */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $userCommon common\models\user\UserCommon */
/* @var $userCard common\models\service\UsersCard */
/* @var $readonly */
?>

<div class="employees-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'employees-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>

    <div class="panel">
        <div class="panel-heading">
            Информация о сотруднике
            <?php if (!$userCommon->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php $user_id = RefBook::find('employees_users')->getValue($model->id); ?>
                <?php if ($user_id): ?>
                    <span class="pull-right"> <?= Html::a('<i class="fa fa-user-o" aria-hidden="true"></i> Регистрационные данные',
                            ['user/default/update', 'id' => $user_id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-default ',
                            ]
                        ); ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <?= $this->render('@backend/views/user/_form', ['form' => $form, 'model' => $userCommon, 'readonly' => $readonly]) ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Служебные данные
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'access_work_flag')->checkbox(['disabled' => false]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if(!$model->access_work_flag || $model->access_work_flag != 1) {
                echo \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> Для получени пропуска необходимо пройти первичный инструктаж по охране труда.',
                    'options' => ['class' => 'alert-info'],
                ]);
            }
            ?>
            <?= $this->render('@backend/views/user/_form_card', ['form' => $form, 'model' => $userCard, 'readonly' => $readonly]) ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
    function toggle(field) {
       if ($(field).is(':checked') ) {
        $('input[name="UsersCard[key_hex]"]').attr("readonly", false);
        $('input[name="UsersCard[timestamp_deny]"]').attr("disabled", false);
    } else {
        $('input[name="UsersCard[key_hex]"]').attr("readonly", true);
        $('input[name="UsersCard[timestamp_deny]"]').attr("disabled", true);
    }
    }
    toggle('input[name="Employees[access_work_flag]"]');
    $('input[name="Employees[access_work_flag]"]').on('click', function () {
        // console.log(this);
       toggle(this);
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>