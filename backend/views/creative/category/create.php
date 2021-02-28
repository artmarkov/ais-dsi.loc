<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeCategory */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['creative/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="creative-category-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>