<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanInvoices */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['invoices/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['invoices/default/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="studyplan-invoices-update">
    <?= $this->render('_form', compact('model')) ?>
</div>