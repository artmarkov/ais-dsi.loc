<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSect */
/* @var $modelsDependence common\models\students\StudentDependence */
/* @var $readonly */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['sect/default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="subject-sect-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>