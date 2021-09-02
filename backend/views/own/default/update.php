<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Invoices */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => sprintf('#%06d', $model->id)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Invoices'), 'url' => ['own/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="invoices-update">
    <?= $this->render('_form', compact('model')) ?>
</div>