<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/education', 'Education Programms'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="education-programm-update">
    <?= $this->render('_form', compact('model')) ?>
</div>