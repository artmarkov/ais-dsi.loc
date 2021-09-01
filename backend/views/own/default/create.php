<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Invoices */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Invoices'), 'url' => ['own/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="invoices-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>