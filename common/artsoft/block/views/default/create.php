<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\block\models\Block */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/block', 'HTML Blocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block-create">
    <?= $this->render('_form', compact('model')) ?>
</div>
