<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\eav\models\EavEntityModel */

$this->title = Yii::t('art', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'Entity Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="eav-entity-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>