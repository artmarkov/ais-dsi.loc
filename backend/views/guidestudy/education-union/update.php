<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationUnion */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Unions'), 'url' => ['guidestudy/education-union/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="education-union-update">
    <?= $this->render('_form', compact('model')) ?>
</div>