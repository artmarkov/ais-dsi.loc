<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Cost */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->directionName . ' - ' . $model->stakeSlug;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Cost'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="cost-update">
    <?= $this->render('_form', compact('model')) ?>
</div>