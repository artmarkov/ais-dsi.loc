<?php
/**
 * @var $this yii\web\View
 * @var $user yeesoft\models\User
 */
use yii\helpers\Html;

$link = Yii::$app->urlManager->createAbsoluteUrl(['/auth/default/confirm-email-receive', 'token' => $user->confirmation_token]);
?>

<div class="email-confirm">
    <p><?= Yii::t('art/auth', 'Hello, {username}.', [
            'username' => Html::encode($user->username)
        ])
        ?></p>

    <p><?= Yii::t('art/auth', 'Follow the link below to confirm your email:') ?></p>

    <p><?= Html::a(Html::encode($link), $link) ?></p>
</div>