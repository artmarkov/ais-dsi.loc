<?php

/* @var $this yii\web\View */
/* @var $model common\models\guidesys\StudentPosition */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Position'), 'url' => ['index']];
$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="student-position-update">
    <?= $this->render('_form', compact('model')) ?>
</div>