<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studygroups\SubjectSect */
/* @var $modelsDependence common\models\students\StudentDependence */
/* @var $readonly */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['studygroups/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="subject-sect-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>