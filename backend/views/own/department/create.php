<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Department */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Department'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="department-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>