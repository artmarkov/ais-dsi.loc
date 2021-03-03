<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\Routine */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routine-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
