<?php
/**
 * @var $this yii\web\View
 * @var $name
 * @var $email
 * @var $subject
 * @var $body
 */
use yii\helpers\Html;

?>

<div class="send-contact">
    <p><b><?= Yii::t('art/mail', 'From') . ':</b> ' . Html::encode($name) ?></p>

    <p><b><?= Yii::t('art/art', 'E-mail') . ':</b> ' . Html::encode($email) ?></p>

    <p><b><?= Yii::t('art/mail', 'Title') . ':</b> ' . Html::encode($subject) ?></p>

    <p><b><?= Yii::t('art/mail', 'Content') . ':</b> ' . Html::encode($body) ?></p>

    <hr>
    <p>Сообщение создано автоматически. Отвечать на него не нужно.</p>
</div>



