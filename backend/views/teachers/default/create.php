<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teachers-create">
    <?= $this->render('_form', ['model' => $model, 'modelUser' => $modelUser, 'modelsActivity' => $modelsActivity]) ?>
</div>