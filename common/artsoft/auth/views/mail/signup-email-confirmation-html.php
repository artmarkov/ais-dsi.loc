<?php
/**
 * @var $this yii\web\View
 * @var $user artsoft\models\User
 */
use yii\helpers\Html;

?>
<?php
$returnUrl = Yii::$app->user->returnUrl == Yii::$app->homeUrl ? null : rtrim(Yii::$app->homeUrl, '/') . Yii::$app->user->returnUrl;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['/auth/default/confirm-registration-email', 'token' => $user->confirmation_token, 'returnUrl' => $returnUrl]);
?>

<?= Yii::t('art/mail', 'Hello, you have been registered on:') ?> <?= Yii::$app->urlManager->hostInfo ?>

    <br/><br/>

    <p><?= Yii::t('art/mail', 'Follow this link to confirm your E-mail and activate account:') ?></p>

<p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>