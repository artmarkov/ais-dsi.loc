<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $model common\models\employees\Employees */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $userCommon common\models\user\UserCommon */
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
                        </div>
                    </div>
                </div>
            </div>

            <?= $this->render('@backend/views/user/_form_card', ['form' => $form, 'model' => $userCard, 'readonly' => $readonly]) ?>

            <!--            --><?php //DynamicFormWidget::begin([
            //                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            //                'widgetBody' => '.container-items', // required: css class selector
            //                'widgetItem' => '.item', // required: css class
            //                'limit' => 4, // the maximum times, an element can be added (default 999)
            //                'min' => 1, // 0 or 1 (default 1)
            //                'insertButton' => '.add-item', // css class
            //                'deleteButton' => '.remove-item', // css class
            //                'model' => $modelsRelations[0],
            //                'formId' => 'student-form',
            //                'formFields' => [
            //                    'work_id',
            //                    'direction_id',
            //                    'stake_id',
            //                ],
            //            ]); ?>

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
