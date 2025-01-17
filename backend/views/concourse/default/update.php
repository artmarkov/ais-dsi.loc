<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\concourse\Concourse */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['concourse/default/index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="concourse-update">
    <?= $this->render('_form', compact('model')) ?>
</div>