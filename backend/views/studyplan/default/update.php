<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $readonly */
/* @var $modelsDependence */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => sprintf('#%06d', $model->id)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="studyplan-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>