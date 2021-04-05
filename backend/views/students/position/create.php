<?php

/* @var $this yii\web\View */
/* @var $model common\models\students\StudentPosition */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['students/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Position'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="student-position-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>