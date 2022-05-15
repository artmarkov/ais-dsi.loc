<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\block\models\Block */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->slug]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/block', 'HTML Blocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>

<div class="block-update">
    <?= $this->render('_form', compact('model')) ?>
</div>


