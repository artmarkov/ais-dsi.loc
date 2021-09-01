<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueDistrict */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','District'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="venue-district-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>