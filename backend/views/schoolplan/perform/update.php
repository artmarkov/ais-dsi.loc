<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanPerform */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Performs'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="schoolplan-perform-update">
    <?= $this->render('_form', compact('model')) ?>
</div>