<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subject\Subject */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="subject-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>