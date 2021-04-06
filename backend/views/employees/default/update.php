<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\employees\Employees */
/* @var $userCommon common\models\user\UserCommon */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/employees', 'Employees'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $model->employeesFullName;
?>
<div class="employees-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
//        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>