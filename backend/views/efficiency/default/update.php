<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\efficiency\TeachersEfficiency */
/* @var $modelDependence  */
/* @var $readonly  */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="teachers-efficiency-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelDependence' => $modelDependence,
        'readonly' => $readonly
    ]) ?>
</div>