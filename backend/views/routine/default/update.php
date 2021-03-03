<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\Routine */

$this->title = Yii::t('art/routine', 'Update Routine: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="routine-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
