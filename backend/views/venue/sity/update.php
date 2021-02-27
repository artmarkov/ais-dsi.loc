<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\venue\VenueSity */

$this->title = Yii::t('art','Update') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Venue Place'), 'url' => ['venue/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Sity'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="venue-sity-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>