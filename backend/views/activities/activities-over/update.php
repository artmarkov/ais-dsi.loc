<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesOver */
/* @var $readonly  */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Activities Overs'), 'url' => ['activities/activities-over/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="activities-over-update">
    <?= $this->render('_form', [
        'model' => $model,
        'readonly' => $readonly
    ]) ?>
</div>