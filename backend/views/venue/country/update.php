<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueCountry */

$this->title = Yii::t('art','Update') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Country'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="venue-country-update">
    <?= $this->render('_form', compact('model')) ?>
</div>