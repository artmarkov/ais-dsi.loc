<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Department */

$this->title = Yii::t('art','Update') . ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Department'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="department-update">
    <?= $this->render('_form', compact('model')) ?>
</div>