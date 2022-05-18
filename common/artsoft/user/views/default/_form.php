<?php

use artsoft\helpers\Html;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ArtHelper;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;

/**
 * @var yii\web\View $this
 * @var artsoft\models $model
 * @var artsoft\widgets\ActiveForm $form
 */
//print_r($model->getVersions());
?>

<div class="user-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'user',
        'validateOnBlur' => false,
    ]);
    ?>
    <div class="panel">
        <div class="panel-heading">
            Информация о пользователе
        </div>
        <div class="panel-body">
            <div class="panel">
                <div class="panel-body">
                    <?php if ($model->userCommon): ?>
                    <?= \yii\widgets\DetailView::widget([
                        'model' => $model->userCommon,
                        'attributes' => [
                            [
                                'attribute' => 'user_category',
                                'value' => \common\models\user\UserCommon::getUserCategoryValue($model->userCommon->user_category),
                            ],
                            'fullName',
                            'birth_date',
                            'phone',
                            'phone_optional',
                            'snils',
//                            'info:ntext',
                            [
                                'attribute' => 'status',
                                'value' => \common\models\user\UserCommon::getStatusValue($model->userCommon->status),
                            ],
                        ],
                    ]) ?>
                </div>
                <div class="panel-footer">
                    <?= Html::a('<i class="fa fa-user-o" aria-hidden="true"></i> Открыть в новом окне',
                        $model->userCommon->getRelatedUrl(),
                        [
                            'target' => '_blank',
                            'class' => 'btn btn-default',
                        ]); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Регистрационные данные
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                            <?php if ($model->isNewRecord): ?>
                                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                                <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                            <?php endif; ?>
                            <?php if (User::hasPermission('editUserEmail')): ?>
                                <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                                <?= $form->field($model, 'email_confirmed')->checkbox() ?>
                            <?php endif; ?>
                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>
                            <?php if (User::hasPermission('viewRegistrationIp')): ?>
                                <?= $form->field($model, 'registration_ip')->textInput(['readonly' => true]) ?>
                            <?php endif; ?>
                            <?php if (User::hasPermission('bindUserToIp')): ?>
                                <?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(Yii::t('art', 'For example') . ' : 123.34.56.78, 234.123.89.78') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if (!$model->isNewRecord): ?>
                    <div class="panel-footer">
                        <div class="form-group btn-group">
                            <?= Html::a('<i class="fa fa-envelope-o" aria-hidden="true"></i> ' . Yii::t('art', 'Send a link to reset your password'),
                                ['/user/default/send-login', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-default btn-sm',
                                    'data' => [
                                        'confirm' => Yii::t('art', 'Are you sure?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            ?>
                            <?php if ($model->status == User::STATUS_ACTIVE): ?>
                                <?= Html::a('<i class="fa fa-user-secret" aria-hidden="true"></i> ' . Yii::t('art', 'Login as user'),
                                    ['/user/default/impersonate', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-warning btn-sm',
                                    ]);
                                ?>
                            <?php endif; ?>
                            <?= Html::a('<i class="fa fa-shield" aria-hidden="true"></i> ' . Yii::t('art/user', 'Permissions'),
                                ['user-permission/set', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-primary btn-sm',

                                ]);
                            ?>

                            <?= Html::a('<i class="fa fa-key" aria-hidden="true"></i> ' . Yii::t('art/user', 'Password'),
                                ['/user/default/change-password', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-danger btn-sm',

                                ]);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model, '/admin/user/default/index', ['/user/default/delete', 'id' => $model->id]); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>











