<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->teachersFullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers','Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="teachers-update">
    <?= $this->render('_form', ['model' => $model, 'modelUser' => $modelUser]) ?>
</div>