<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationUnion */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Unions'), 'url' => ['guidestudy/education-union/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['guidestudy/education-union/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="education-union-update">
    <?= $this->render('_form', compact('model')) ?>
</div>