<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenuePlace */

$this->title = Yii::t('art','Update') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="venue-place-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>