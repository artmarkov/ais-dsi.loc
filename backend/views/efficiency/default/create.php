<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\efficiency\TeachersEfficiency */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/efficiency', 'Teachers Efficiencies'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teachers-efficiency-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>