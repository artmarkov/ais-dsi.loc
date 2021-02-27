<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueCountry */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Country'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="venue-country-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>