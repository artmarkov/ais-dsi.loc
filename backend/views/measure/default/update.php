<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Measure */

$this->title = 'Update Measure: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Measures', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="measure-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>