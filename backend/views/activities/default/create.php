<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */

$this->title = Yii::t('art', 'New entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities'), 'url' => ['activities/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="activities-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>