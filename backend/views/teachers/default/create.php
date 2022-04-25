<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */
/* @var $userCommon common\models\user\UserCommon */
/* @var $userCard common\models\service\UsersCard */
/* @var $modelsActivity common\models\teachers\TeachersActivity */
/* @var $readonly */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Create');
?>

<div class="teachers-create">
    <?= $this->render('_form', [
        'model' => $model,
        'userCommon' => $userCommon,
        'userCard' => $userCard,
        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>