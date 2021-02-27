<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\calendar\EventCategory */

$this->title = 'Create Event Category';
$this->params['breadcrumbs'][] = ['label' => 'Event Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="event-category-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>