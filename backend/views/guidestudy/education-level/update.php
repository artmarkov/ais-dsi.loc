<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationLevel */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Levels'), 'url' => ['education/education-level/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="education-level-update">
    <?= $this->render('_form', compact('model')) ?>
</div>