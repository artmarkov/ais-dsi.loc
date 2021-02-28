<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Division */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Division'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="division-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>