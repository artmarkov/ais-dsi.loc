<?php

/**
 * @var $this yii\web\View
 * @var $user yeesoft\models\User
 */
use yii\helpers\Html;

$returnUrl = Yii::$app->user->returnUrl == Yii::$app->homeUrl ? null : rtrim(Yii::$app->homeUrl, '/') . Yii::$app->user->returnUrl;
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['/auth/default/confirm-registration-email', 'token' => $user->confirmation_token, 'returnUrl' => $returnUrl]);
?>

<div class="signup-email-confirm">
    <p><?= Yii::t('art/auth', 'Hello, you have been registered on {host}.',[
        'host' => Yii::$app->urlManager->hostInfo
    ]) ?></p>

    <br/><br/>

    <p><?= Yii::t('art/auth', 'Follow this link to confirm your E-mail and activate account:') ?></p>

    <p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>
</div>