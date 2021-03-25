<?php

use artsoft\Art;

/* @var $this yii\web\View */
?>

<?php $info = \artsoft\models\UserVisitLog::getLastVisit();?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading"><?= Yii::t('art', 'System Info') ?></div>
    <div class="panel-body">
        <b><?= Yii::t('art', 'Art CMS Version') ?>:</b> <?= Yii::$app->params['version']; ?><br/>
        <b><?= Yii::t('art', 'Art Core Version') ?>:</b> <?= Art::getVersion(); ?><br/>
        <b><?= Yii::t('art', 'Yii Framework Version') ?>:</b> <?= Yii::getVersion(); ?><br/>
        <b><?= Yii::t('art', 'PHP Version') ?>:</b> <?= phpversion(); ?><br/>
        <hr>
        <b><?= Yii::t('art', 'Previous successful login') ?>:</b><br/> <?= $info ?  Yii::$app->formatter->asDatetime($info['visit_time'], 'php:d.m.Y h:i:s') . '<br/>ip: ' . $info['ip'] : ''; ?><br/>
    </div>
</div>