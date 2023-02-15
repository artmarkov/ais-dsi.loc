<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Teachers Plan'), 'url' => ['indivplan/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="indivplan-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>