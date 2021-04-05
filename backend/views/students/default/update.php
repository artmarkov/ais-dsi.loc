<?php

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->studentsFullName;
?>
<div class="student-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
//        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>