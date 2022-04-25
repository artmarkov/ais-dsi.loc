<?php

/* @var $this yii\web\View */
/* @var $model common\models\students\Student */
/* @var $userCommon common\models\user\UserCommon */
/* @var $userCard common\models\service\UsersCard */
/* @var $modelsDependence common\models\students\StudentDependence */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="student-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        'userCard' => $userCard,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>