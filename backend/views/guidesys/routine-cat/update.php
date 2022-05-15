<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\RoutineCat */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routine Cats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="routine-cat-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
