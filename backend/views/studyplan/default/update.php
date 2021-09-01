<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $readonly */
/* @var $modelsDependence */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="studyplan-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>