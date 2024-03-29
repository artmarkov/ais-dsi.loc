<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\efficiency\TeachersEfficiency */
/* @var $modelDependence  */
/* @var $readonly  */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teachers-efficiency-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelDependence' => $modelDependence,
        'readonly' => $readonly
    ]) ?>
</div>