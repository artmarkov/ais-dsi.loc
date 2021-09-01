<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Level */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Level'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="level-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>