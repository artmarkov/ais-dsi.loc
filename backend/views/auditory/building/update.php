<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\auditory\AuditoryBuilding */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory'), 'url' => ['auditory/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Building'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="auditory-building-update">
    <?=  $this->render('_form', compact('model')) ?>
</div>