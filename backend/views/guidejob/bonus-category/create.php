<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\BonusCategory */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Bonus Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bonus-category-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>