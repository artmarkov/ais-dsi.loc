<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\PieceCategory */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => sprintf('#%06d', $model->id)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Piece Categories'), 'url' => ['guidestudy/piece-category/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="piece-category-update">
    <?= $this->render('_form', compact('model')) ?>
</div>