<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $readonly  */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schoolplan-plan-create">
    <?= $this->render('_form', [
        'model' => $model,
        'readonly' => $readonly
    ]) ?>
</div>