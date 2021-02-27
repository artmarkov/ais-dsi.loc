<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Direction */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Direction'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="direction-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>