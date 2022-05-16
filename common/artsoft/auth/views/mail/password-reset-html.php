<?php
/**
 * @var $this yii\web\View
 * @var $user artsoft\models\User
 */
use yii\helpers\Html;
use yii\helpers\Url;

$resetLink = Yii::$app->request->hostInfo . '/auth/default/reset-password-request?token=' . $user->confirmation_token;
?>

<div class="password-reset">
    <p><?= Yii::t('art/mail', 'Hello:') ?> <?= Html::encode($user->username) ?>.</p>

    <p><?= Yii::t('art/mail', 'Follow the link below to reset your password:') ?></p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>