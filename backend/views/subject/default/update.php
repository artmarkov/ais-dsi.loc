<?php

/* @var $this yii\web\View */
/* @var $model common\models\subject\Subject */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="subject-update">
    <?= $this->render('_form', compact('model')) ?>
</div>