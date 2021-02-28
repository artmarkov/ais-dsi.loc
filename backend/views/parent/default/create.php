<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserCommon */

$this->title = Yii::t('art','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Parents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parents-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>