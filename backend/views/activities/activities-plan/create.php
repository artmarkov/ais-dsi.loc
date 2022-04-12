<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesPlan */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Activities Plans'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="activities-plan-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>