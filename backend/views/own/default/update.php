<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Invoices */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Invoices'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoices-update">
    <?= $this->render('_form', compact('model')) ?>
</div>