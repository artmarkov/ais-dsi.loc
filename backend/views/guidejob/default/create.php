<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Work */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Work'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="work-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>