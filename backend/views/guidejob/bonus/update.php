<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\BonusItem */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers Bonus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);

?>
<div class="bonus-item-update">
    <?= $this->render('_form', compact('model')) ?>
</div>