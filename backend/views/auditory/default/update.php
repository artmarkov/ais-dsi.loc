<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Auditory */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="auditory-update">
    <?=  $this->render('_form', compact('model')) ?>
</div>