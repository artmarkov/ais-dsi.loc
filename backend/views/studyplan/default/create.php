<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $readonly */
/* @var $modelsDependence */

//$this->title = Yii::t('art', 'Create');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="studyplan-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly,
        'indexAction' => $indexAction
    ]) ?>
</div>