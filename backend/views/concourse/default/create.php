<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\concourse\Concourse */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => 'Конкурсы', 'url' => ['concourse/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="concourse-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>