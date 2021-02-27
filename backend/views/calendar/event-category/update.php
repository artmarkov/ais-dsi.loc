<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\calendar\EventCategory */

$this->title = 'Update Event Category: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-category-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>