<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\employees\Employees */
/* @var $userCommon common\models\user\UserCommon */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/employees', 'Employees'), 'url' => ['employees/default/index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Create');
?>

<div class="employees-create">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        'readonly' => $readonly
    ]) ?>
</div>