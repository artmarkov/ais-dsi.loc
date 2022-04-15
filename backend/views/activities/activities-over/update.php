<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesOver */
/* @var $readonly  */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Activities Overs'), 'url' => ['activities/activities-over/index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['activities/activities-over/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="activities-over-update">
    <?= $this->render('_form', [
        'model' => $model,
        'readonly' => $readonly
    ]) ?>
</div>