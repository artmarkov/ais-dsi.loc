<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\RoutineCat */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routine Cats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="routine-cat-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
