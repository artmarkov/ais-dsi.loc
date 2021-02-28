<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AuditoryBuilding */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory'), 'url' => ['auditory/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Buildung'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="auditory-building-update">
    <?=  $this->render('_form', compact('model')) ?>
</div>