<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Create');
?>

<div class="teachers-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
        'modelsActivity' => $modelsActivity,
        'readonly' => $readonly
    ]) ?>
</div>