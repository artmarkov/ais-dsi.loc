<?php

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */
/* @var $userCommon common\models\user\UserCommon */
/* @var $readonly */

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