<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersCard */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Users Cards'), 'url' => ['service/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="users-card-update">
    <?= $this->render('_form', compact('model')) ?>
</div>