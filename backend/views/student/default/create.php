<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="student-create">
    <?= $this->render('_form', ['model' => $model, 'modelUser' => $modelUser]) ?>
</div>