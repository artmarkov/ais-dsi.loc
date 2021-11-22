<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studygroups\SubjectSect */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['studygroups/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="subject-sect-update">
    <?= $this->render('_form', compact('model')) ?>
</div>