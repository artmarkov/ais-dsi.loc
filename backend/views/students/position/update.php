<?php

/* @var $this yii\web\View */
/* @var $model common\models\students\StudentPosition */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['students/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Position'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="student-position-update">
    <?= $this->render('_form', compact('model')) ?>
</div>