<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesCat */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities Cats'), 'url' => ['guidesys/activities-cat/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="activities-cat-update">
    <?= $this->render('_form', compact('model')) ?>
</div>