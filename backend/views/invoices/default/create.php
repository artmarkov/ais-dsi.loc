<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanInvoices */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['invoices/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="studyplan-invoices-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>