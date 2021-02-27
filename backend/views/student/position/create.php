<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\student\StudentPosition */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Students'), 'url' => ['student/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/student','Name Position'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="student-position-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>