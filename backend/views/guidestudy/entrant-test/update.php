<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\GuideEntrantTest */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Tests'), 'url' => ['/guidestudy/entrant-test']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="guide-entrant-test-update">
    <?= $this->render('_form', compact('model')) ?>
</div>