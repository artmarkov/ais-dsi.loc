<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesCat */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities Cats'), 'url' => ['activities/activities-cat/index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activities-cat-update">
    <?= $this->render('_form', compact('model')) ?>
</div>