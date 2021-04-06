<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\guidesys\UserRelation */

$this->title = Yii::t('art','Update'). ' : ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'User Relations'), 'url' => ['guidesys/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['guidesys/default/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-relation-update">
    <?= $this->render('_form', compact('model')) ?>
</div>