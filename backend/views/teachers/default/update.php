<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */
/* @var $userCommon common\models\user\UserCommon */
/* @var $modelsActivity common\models\teachers\TeachersActivity */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->teachersFullName;
?>
<div class="teachers-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>