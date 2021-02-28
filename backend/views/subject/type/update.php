<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subject\SubjectType */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Subjects'), 'url' => ['subject/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Subject Type'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="subject-type-update">
    <?= $this->render('_form', compact('model')) ?>
</div>