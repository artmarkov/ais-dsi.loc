<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities'), 'url' => ['activities/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="activities-update">
    <?= $this->render('_form', compact('model')) ?>
</div>