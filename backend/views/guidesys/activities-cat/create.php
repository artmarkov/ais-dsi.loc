<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesCat */

$this->title = Yii::t('art', 'New entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities Cats'), 'url' => ['guidesys/activities-cat/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="activities-cat-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>