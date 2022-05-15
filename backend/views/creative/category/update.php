<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeCategory */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Works'), 'url' => ['creative/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/creative','Creative Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="creative-category-update">
    <?=  $this->render('_form', compact('model')) ?>
</div>