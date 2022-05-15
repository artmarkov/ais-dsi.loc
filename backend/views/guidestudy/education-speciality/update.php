<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationSpeciality */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Specializations'), 'url' => ['education/education-speciality/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="education-speciality-update">
    <?= $this->render('_form', compact('model')) ?>
</div>