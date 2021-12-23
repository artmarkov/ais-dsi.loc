<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $readonly */
/* @var $modelsStudyplanSubject */

?>

<div class="studyplan-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsStudyplanSubject' => $modelsStudyplanSubject,
        'readonly' => $readonly
    ]) ?>
</div>