<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AuditoryCat */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory'), 'url' => ['auditory/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Auditory Cat'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="auditory-cat-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>