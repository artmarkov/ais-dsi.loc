<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/info', 'Board'), 'url' => ['info/board/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="board-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>