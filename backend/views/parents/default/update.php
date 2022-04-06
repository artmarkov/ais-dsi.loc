<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\parents\Parents */
/* @var $userCommon common\models\user\UserCommon */
/* @var $userCard common\models\sigur\UsersCard */
/* @var $modelsDependence common\models\students\StudentDependence */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/parents', 'Parents'), 'url' => ['parents/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="parents-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        'userCard' => $userCard,
        'modelsDependence' => $modelsDependence,
        'readonly' => $readonly
    ]) ?>
</div>