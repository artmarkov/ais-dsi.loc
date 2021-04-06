<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\parents\Parents */
/* @var $userCommon common\models\user\UserCommon */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/parents', 'Parents'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = Yii::t('art','Create');
?>

<div class="parents-create">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        // 'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>