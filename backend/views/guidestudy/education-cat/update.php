<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationCat */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . sprintf('#%06d', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Cats'), 'url' => ['education/education-cat/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="education-cat-update">
    <?= $this->render('_form', compact('model')) ?>
</div>