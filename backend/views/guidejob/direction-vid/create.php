<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\DirectionVid */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Direction Vid'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="direction-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>