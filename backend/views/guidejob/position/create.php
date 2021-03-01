<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Position */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Position'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="position-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>