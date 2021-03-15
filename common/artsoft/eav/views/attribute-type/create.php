<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\eav\models\EavAttributeType */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/eav', 'Attribute Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="eav-attribute-type-create">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>