<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Cost */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Cost'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cost-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>