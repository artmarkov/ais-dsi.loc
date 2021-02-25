<?php

/**
 * @var yii\web\View $this
 * @var artsoft\models\User $user
 */

$this->title = Yii::t('art/auth', 'E-mail confirmed');
?>
<div class="change-own-password-success">

    <div class="alert alert-success text-center">
        <?= Yii::t('art/auth', 'E-mail confirmed') ?> - <b><?= $user->email ?></b>
    </div>

</div>
