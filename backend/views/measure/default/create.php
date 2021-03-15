<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Measure */

$this->title = 'Create Measure';
$this->params['breadcrumbs'][] = ['label' => 'Measures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="measure-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>