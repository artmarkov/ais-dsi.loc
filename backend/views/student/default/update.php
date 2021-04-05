<?php

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->studentsFullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="student-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
//        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>