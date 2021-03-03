<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\RoutineCat */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/routine', 'Routine Cats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routine-cat-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
