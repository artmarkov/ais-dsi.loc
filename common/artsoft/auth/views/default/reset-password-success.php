<?php

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('art/auth', 'Password recovery');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="password-recovery-success">
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
         <?= Yii::t('art/auth', 'Check your E-mail for further instructions') ?>
    </div>
</div>
