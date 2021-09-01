<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueSity */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Sity'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="venue-sity-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>