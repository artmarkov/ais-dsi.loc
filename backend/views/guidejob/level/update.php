<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Level */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Level'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="level-update">
    <?= $this->render('_form', compact('model')) ?>
</div>