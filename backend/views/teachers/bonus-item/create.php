<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\BonusItem */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['teachers/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers Bonus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bonus-item-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>