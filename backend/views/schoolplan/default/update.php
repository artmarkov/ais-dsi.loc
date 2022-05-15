<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $readonly  */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="schoolplan-plan-update">
    <?= $this->render('_form', [
        'model' => $model,
        'readonly' => $readonly
    ]) ?>
</div>