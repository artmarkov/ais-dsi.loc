<?php
/**
 * @var $this yii\web\View
 * @var $user artsoft\models\User
 */
use yii\helpers\Html;

?>
<?php
$returnUrl = Yii::$app->user->returnUrl == Yii::$app->homeUrl ? null : rtrim(Yii::$app->homeUrl, '/') . Yii::$app->user->returnUrl;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['/auth/default/confirm-registration-email', 'token' => $user->confirmation_token, 'returnUrl' => $returnUrl], 'https');
?>

<p><?= Yii::t('art/mail', 'Hello:') ?> <b><?= Html::encode($user->username) ?></b></p>

<br/><br/>

<p><?= Yii::t('art/mail', 'Follow the link below to confirm your email:') ?></p>

<p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>