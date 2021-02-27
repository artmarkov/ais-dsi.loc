<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\Department */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Division'), 'url' => ['/service/division/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide','Department'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="department-create">
    <h3 class="lte-hide-title"><?=  Html::encode($this->title) ?></h3>
    <?=  $this->render('_form', compact('model')) ?>
</div>