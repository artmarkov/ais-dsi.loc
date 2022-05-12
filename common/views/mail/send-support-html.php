<?php
/**
 * @var $this yii\web\View
 * @var $email
 * @var $phone
 * @var $phone_optional
 * @var $subject
 * @var $body
 */
use yii\helpers\Html;

?>

<div class="send-supoprt">
    <p><b><?= Yii::t('art/mail', 'From') . ':</b> ' . Html::encode($email) ?></p>

    <p><b><?= Yii::t('art/mail', 'Phone') . ':</b> ' . Html::encode($phone) . '  ' . Html::encode($phone_optional) ?></p>

    <p><b><?= Yii::t('art/mail', 'Title') . ':</b> ' . Html::encode($subject) ?></p>

    <p><b><?= Yii::t('art/mail', 'Content') . ':</b> ' . Html::encode($body) ?></p>

    <hr>
    <p>Сообщение создано автоматически. Отвечать на него не нужно.</p>
</div>



