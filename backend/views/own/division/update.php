<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Division */

$this->title = Yii::t('art','Update') . ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Division'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="division-update">
    <?= $this->render('_form', compact('model')) ?>
</div>