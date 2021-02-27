<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\Department */

$this->title = Yii::t('art','Update') . ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Division'), 'url' => ['/service/division/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Department'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('art','Update');
?>
<div class="department-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>