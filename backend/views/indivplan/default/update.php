<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */

$this->title = Yii::t('art', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['indivplan/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['indivplan/default/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indivplan-update">
    <?= $this->render('_form', compact('model')) ?>
</div>