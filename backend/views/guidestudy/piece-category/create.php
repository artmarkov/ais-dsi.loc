<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\PieceCategory */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Piece Categories'), 'url' => ['guidestudy/piece-category/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="piece-category-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>