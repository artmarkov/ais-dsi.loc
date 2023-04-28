<?php

use artsoft\auth\assets\AvatarAsset;
use artsoft\auth\assets\AvatarUploaderAsset;
use artsoft\auth\widgets\AuthChoice;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use artsoft\helpers\artHelper;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var artsoft\auth\models\forms\SetEmailForm $user
 * @var artsoft\auth\models\forms\ProfileForm $userCommon
 */
$this->title = Yii::t('art/auth', 'User Profile');
$this->params['breadcrumbs'][] = $this->title;

AvatarUploaderAsset::register($this);
AvatarAsset::register($this);

$info = \artsoft\models\UserVisitLog::getLastVisit();
?>

<div class="profile-index">
    <div class="panel">
        <div class="panel-body">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-9">
                    <span class="h4"><?= $this->title ?></span>
                </div>
                <div class="text-right col-md-3">
                    <?php if (User::hasPermission('changeOwnPassword')): ?>
                        <?= Html::a(Yii::t('art/auth', 'Update Password'), ['/auth/default/update-password'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="image-uploader">
                                <?php
                                ActiveForm::begin([
                                    'method' => 'post',
                                    'action' => Url::to(['/auth/default/upload-avatar']),
                                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                                ])
                                ?>

                                <?php $avatar = ($userAvatar = Yii::$app->user->identity->getAvatar('large')) ? $userAvatar : AvatarAsset::getDefaultAvatar('large') ?>
                                <div class="image-preview" data-default-avatar="<?= $avatar ?>">
                                    <img src="<?= $avatar ?>"/>
                                </div>
                                <div class="image-actions">
                                <span class="btn btn-primary btn-file"
                                      title="<?= Yii::t('art/auth', 'Change profile picture') ?>" data-toggle="tooltip"
                                      data-placement="bottom">
                                    <i class="fa fa-folder-open"></i>
                                    <?= Html::fileInput('image', null, ['class' => 'image-input']) ?>
                                </span>

                                    <?=
                                    Html::submitButton('<i class="fa fa-save"></i>', [
                                        'class' => 'btn btn-primary image-submit',
                                        'title' => Yii::t('art/auth', 'Save profile picture'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'bottom',
                                    ])
                                    ?>
                                    <span class="btn btn-primary image-remove"
                                          data-action="<?= Url::to(['/auth/default/remove-avatar']) ?>"
                                          title="<?= Yii::t('art/auth', 'Remove profile picture') ?>"
                                          data-toggle="tooltip" data-placement="bottom">
                                <i class="fa fa-remove"></i>
                                </span>
                                </div>
                                <div class="upload-status"></div>

                                <?php ActiveForm::end() ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="record-info">
                                <div class="form-group clearfix">
                                    <label class="control-label" style="float: left; padding-right: 5px;">
                                        <?= $user->attributeLabels()['username'] ?> :
                                    </label>
                                    <span><?= $user->username; ?></span>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="control-label" style="float: left; padding-right: 5px;">

                                        <?= $userCommon->attributeLabels()['user_category'] ?>
                                    </label>
                                    <span>
                                    <?= \common\models\user\UserCommon::getUserCategoryValue($userCommon->user_category); ?>

                                </div>
                                <div class="form-group clearfix">
                                    <label class="control-label" style="float: left; padding-right: 5px;">
                                        <?= $user->attributeLabels()['created_at'] ?> :
                                    </label>
                                    <span><?= $user->createdDatetime; ?></span>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="control-label" style="float: left; padding-right: 5px;">
                                        <?= $user->attributeLabels()['updated_at'] ?> :
                                    </label>
                                    <span><?= $user->updatedDatetime; ?></span>
                                </div>
                                <hr>
                                <div class="form-group clearfix">
                                    <label class="control-label" style="float: left; padding-right: 5px;">
                                        <?= Yii::t('art', 'Previous successful login') ?> :
                                    </label>
                                    <label class="control-label"
                                           style="float: left; padding-right: 5px;"><?= $info ? Yii::$app->formatter->asDatetime($info['visit_time'], 'php:d.m.Y h:i:s') . '<br/>ip: ' . $info['ip'] : ''; ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $form = ActiveForm::begin([
                    'id' => 'user',
                    'validateOnBlur' => false,
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ])
                ?>
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($user, 'username')->textInput(['maxlength' => 255, 'autofocus' => false, 'readonly' => true]) ?>
                                <?= $form->field($userCommon, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput(['readonly' => $userCommon->snils == '' ? false : true])->hint('Может потребоваться для восстановления учетных данных.') ?>
                                <?= $form->field($user, 'email')->textInput(['maxlength' => 255, 'autofocus' => false])->hint(Yii::t('art/auth', 'After changing the E-mail confirmation is required')) ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($userCommon, 'last_name')->textInput(['maxlength' => 124]) ?>
                                <?= $form->field($userCommon, 'first_name')->textInput(['maxlength' => 124]) ?>
                                <?= $form->field($userCommon, 'middle_name')->textInput(['maxlength' => 124])->hint('Важно: Поле необходимо заполнить как в документе. При отсутствии Отчества заполнение не требуется.') ?>
                                <?= $form->field($userCommon, 'gender')->dropDownList(\common\models\user\UserCommon::getGenderList()) ?>
                                <?= $form->field($userCommon, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(\kartik\date\DatePicker::classname()); ?>
                                <?= $form->field($userCommon, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                <?= $form->field($userCommon, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                <?= $form->field($userCommon, 'address')->textInput(['maxlength' => 124]) ?>
                            </div>
                        </div>
                    </div>
                    <?= Html::submitButton(Yii::t('art/auth', 'Save Profile'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$confRemovingAuthMessage = Yii::t('art/auth', 'Are you sure you want to unlink this authorization?');
$confRemovingAvatarMessage = Yii::t('art/auth', 'Are you sure you want to delete your profile picture?');
$js = <<<JS
confRemovingAuthMessage = "{$confRemovingAuthMessage}";
confRemovingAvatarMessage = "{$confRemovingAvatarMessage}";
JS;

$this->registerJs($js, yii\web\View::POS_READY);
?>
