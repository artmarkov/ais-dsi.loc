<?php
/**
 * @var $this yii\web\View
 * @var $user artsoft\models\User
 */
use yii\helpers\Html;

$resetLink = Yii::$app->request->hostInfo . '/auth/default/reset-password-request?token=' . $user->confirmation_token;
$resetLink = str_replace('http://', 'https://', $resetLink);
?>

<div class="password-reset">
    <p><?= Yii::t('art/mail', 'Hello:') ?> <b><?= Html::encode($user->username) ?></b></p>

    <p><?= Yii::t('art/mail', 'Follow the link below to reset your password:') ?></p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>