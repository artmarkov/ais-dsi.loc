<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $readonly */
/* @var $modelsDependence */

?>

<div class="studyplan-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>