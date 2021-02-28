<?php

use artsoft\auth\assets\AvatarAsset;
use artsoft\auth\assets\AvatarUploaderAsset;
use artsoft\auth\widgets\AuthChoice;
//use yii\bootstrap\ActiveForm;
use artsoft\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use artsoft\helpers\artHelper;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var artsoft\auth\models\forms\SetEmailForm $model
 */
$this->title = Yii::t('art/auth', 'User Profile');
$this->params['breadcrumbs'][] = $this->title;

AvatarUploaderAsset::register($this);
AvatarAsset::register($this);

$col12 = $this->context->module->gridColumns;
$col9 = (int) ($col12 * 3 / 4);
$col6 = (int) ($col12 / 2);
$col4 = (int) ($col12 / 3);
$col3 = (int) ($col12 / 4);
?>

<div class="profile-index">

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-<?= $col9 ?>">
            <span class="h4"><?= $this->title ?></span>
        </div>
        <div class="text-right col-md-<?= $col3 ?>">
            <?= Html::a(Yii::t('art/auth', 'Update Password'), ['/auth/default/update-password'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-<?= $col3 ?>">
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
                                  data-placement="left">
                                <i class="fa fa-folder-open"></i>
                                <?= Html::fileInput('image', null, ['class' => 'image-input']) ?>
                            </span>

                            <?=
                            Html::submitButton('<i class="fa fa-save"></i>', [
                                'class' => 'btn btn-primary image-submit',
                                'title' => Yii::t('art/auth', 'Save profile picture'),
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                            ])
                            ?>

                            <span class="btn btn-primary image-remove"
                                  data-action="<?= Url::to(['/auth/default/remove-avatar']) ?>"
                                  title="<?= Yii::t('art/auth', 'Remove profile picture') ?>" data-toggle="tooltip"
                                  data-placement="right">
                                <i class="fa fa-remove"></i>
                            </span>
                        </div>
                        <div class="upload-status"></div>

                        <?php ActiveForm::end() ?>
                    </div>

                    <!-- <div class="oauth-services">
                         <div class="oauth-authorized-services">
                             <div class="label label-primary space-down"
                                  title="<?/*= Yii::t('art/auth', 'Click to unlink service') */?>" data-toggle="tooltip"
                                  data-placement="right">
                                 <?/*= Yii::t('art/auth', 'Authorized Services') */?>:
                             </div>

                             <?/*=
                             AuthChoice::widget([
                                 'baseAuthUrl' => ['/auth/default/unlink-oauth', 'language' => false],
                                 'displayClients' => AuthChoice::DISPLAY_AUTHORIZED,
                                 'popupMode' => false,
                                 'shortView' => true,
                             ])
                             */?>
                         </div>

                         <div>
                             <div class="label label-primary space-down"
                                  title="<?/*= Yii::t('art/auth', 'Click to connect with service') */?>" data-toggle="tooltip"
                                  data-placement="right">
                                 <?/*= Yii::t('art/auth', 'Non Authorized Services') */?>:
                             </div>

                             <?/*=
                             AuthChoice::widget([
                                 'baseAuthUrl' => ['/auth/default/oauth', 'language' => false],
                                 'displayClients' => AuthChoice::DISPLAY_NON_AUTHORIZED,
                                 'popupMode' => false,
                                 'shortView' => true,
                             ])
                             */?>
                         </div>
                     </div>-->

                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;">
                                <?= $model->attributeLabels()['username'] ?> :
                            </label>
                            <span><?= $model->username; ?></span>
                        </div>
<!--                        <div class="form-group clearfix">-->
<!--                            <label class="control-label" style="float: left; padding-right: 5px;">-->
<!--                                --><?//= $model->attributeLabels()['user_category'] ?><!-- :-->
<!--                            </label>-->
<!--                            <span>--><?//= \common\models\user\User::getUserCategoryValue($model->user_category); ?><!--</span>-->
<!---->
<!--                        </div>-->
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;">
                                <?= $model->attributeLabels()['created_at'] ?> :
                            </label>
                            <span><?= $model->createdDatetime; ?></span>
                        </div>
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;">
                                <?= $model->attributeLabels()['updated_at'] ?> :
                            </label>
                            <span><?= $model->updatedDatetime; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $form = ActiveForm::begin([
                    'id' => 'user',
                    'validateOnBlur' => false,
                ])
        ?>

        <div class="col-md-<?= $col9 ?>">

            <div class="panel panel-default">
                <div class="panel-body">

                    <!--                    --><?//= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autofocus' => false]) ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'autofocus' => false])->hint(Yii::t('art/auth', 'After changing the E-mail confirmation is required')) ?>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-<?= $col4 ?>">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-<?= $col4 ?>">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-<?= $col4 ?>">
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-<?= $col3 ?>">
                            <?= $form->field($model, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>
                        </div>
<!--                        <div class="col-md---><?//= $col3 ?><!--">-->
<!--                            --><?php // if($model->birth_timestamp) $model->birth_timestamp = date("d-m-Y", (integer) mktime(0,0,0, date("m", $model->birth_timestamp), date("d", $model->birth_timestamp), date("Y", $model->birth_timestamp)));  ?>
<!--                            --><?//= $form->field($model, 'birth_timestamp')->widget(MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>
<!--                        </div>-->
                        <div class="col-md-<?= $col3 ?>">
<!--                            --><?//= $form->field($model, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-<?= $col6 ?>">
                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                        <div class="col-md-<?= $col6 ?>">
<!--                            --><?//= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'info')->textarea(['rows' => 10, 'maxlength' => 1024]) ?>

                </div>
            </div>

            <?= Html::submitButton(Yii::t('art/auth', 'Save Profile'), ['class' => 'btn btn-primary']) ?>


        </div>
        <?php ActiveForm::end(); ?>
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
